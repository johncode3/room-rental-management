<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(): View
    {
        $contracts = Contract::with(['tenant', 'room.roomType'])
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->latest('start_date')
            ->get();

        return view('contracts.index', compact('contracts'));
    }

    public function create(): View
    {
        $rooms = Room::with('roomType')
            ->where('status', 'available')
            ->orderBy('room_number')
            ->get();
        $tenants = Tenant::whereDoesntHave('contracts', fn ($query) => $query->where('status', 'active'))
            ->orderBy('full_name')
            ->get();

        return view('contracts.create', compact('rooms', 'tenants'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'existing_tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'full_name' => ['required_without:existing_tenant_id', 'string', 'max:255'],
            'phone' => ['required_without:existing_tenant_id', 'string', 'max:30', 'unique:tenants,phone'],
            'national_id' => ['required_without:existing_tenant_id', 'string', 'max:50', 'unique:tenants,national_id'],
            'emergency_contact' => ['nullable', 'string', 'max:100'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $contract = DB::transaction(function () use ($validated): ?Contract {
            $room = Room::query()
                ->whereKey($validated['room_id'])
                ->lockForUpdate()
                ->first();

            if (! $room || $room->status !== 'available') {
                return null;
            }

            if (isset($validated['existing_tenant_id'])) {
                $tenant = Tenant::query()
                    ->lockForUpdate()
                    ->findOrFail($validated['existing_tenant_id']);

                if ($tenant->contracts()->where('status', 'active')->lockForUpdate()->first()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'existing_tenant_id' => 'អ្នកជួលនេះកំពុងមានកិច្ចសន្យាសកម្មរួចហើយ (This tenant already has an active contract).',
                    ]);
                }
            } else {
                $tenant = Tenant::create([
                    'full_name' => $validated['full_name'],
                    'phone' => $validated['phone'],
                    'national_id' => $validated['national_id'],
                    'emergency_contact' => $validated['emergency_contact'] ?? null,
                ]);
            }

            $contract = Contract::create([
                'tenant_id' => $tenant->id,
                'room_id' => $room->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'deposit_amount' => $validated['deposit_amount'],
                'status' => 'active',
            ]);

            $room->update(['status' => 'occupied']);

            return $contract;
        });

        if (! $contract) {
            return back()
                ->withInput()
                ->with('error', 'បន្ទប់នេះមិនទំនេរទៀតទេ។ សូមជ្រើសរើសបន្ទប់ផ្សេង (This room is no longer available. Please choose another room).');
        }

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'បានចុះកិច្ចសន្យាជួលថ្មីដោយជោគជ័យ (Check-in completed successfully).');
    }

    public function show(Contract $contract): View
    {
        $contract->load([
            'tenant',
            'room.roomType',
            'meterReadings',
            'invoices' => fn ($query) => $query->with('meterReading')->latest('invoice_date'),
        ]);

        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract): View
    {
        $contract->load(['tenant', 'room.roomType']);

        return view('contracts.edit', compact('contract'));
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('tenants', 'phone')->ignore($contract->tenant_id),
            ],
            'national_id' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('tenants', 'national_id')->ignore($contract->tenant_id),
            ],
            'emergency_contact' => ['nullable', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $contract): void {
            $contract->tenant->update([
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'national_id' => $validated['national_id'] ?? null,
                'emergency_contact' => $validated['emergency_contact'] ?? null,
            ]);

            $contract->update([
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'deposit_amount' => $validated['deposit_amount'],
            ]);
        });

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'បានកែប្រែព័ត៌មានអ្នកជួល និងកិច្ចសន្យាដោយជោគជ័យ (Contract updated successfully).');
    }

    public function checkout(Contract $contract): RedirectResponse
    {
        if ($contract->invoices()->where('payment_status', 'unpaid')->exists()) {
            return back()
                ->with('error', 'មិនអាចបញ្ចប់កិច្ចសន្យាបានទេ ព្រោះអ្នកជួលនៅជំពាក់វិក្កយបត្រ (Cannot checkout with unpaid invoices).');
        }

        DB::transaction(function () use ($contract): void {
            $contract->update([
                'status' => 'ended',
                'end_date' => now()->toDateString(),
            ]);
            $contract->room()->update(['status' => 'available']);
        });

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'បានបញ្ចប់កិច្ចសន្យា និង Check-out ដោយជោគជ័យ (Check-out completed successfully).');
    }
}
