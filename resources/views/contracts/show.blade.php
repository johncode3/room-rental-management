@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl">
    @if (session('success'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800" role="alert">{{ session('error') }}</div>
    @endif

    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">ព័ត៌មានកិច្ចសន្យា / Contract Details</h1>
            <p class="mt-1 text-sm text-gray-500">Room {{ $contract->room->room_number }} · {{ $contract->tenant->full_name }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if ($contract->status === 'active')
                <a href="{{ route('contracts.edit', $contract) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100" title="Edit Contract" aria-label="Edit Contract">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
            @endif
            <a href="{{ route('contracts.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50" title="Back to Contracts" aria-label="Back to Contracts">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold text-gray-900">កិច្ចសន្យា និងអ្នកជួល / Contract &amp; Tenant</h2>
            <dl class="space-y-4 text-sm">
                <div><dt class="text-gray-500">បន្ទប់ / Room</dt><dd class="mt-1 font-semibold text-gray-900">{{ $contract->room->room_number }} — {{ $contract->room->roomType->type_name }}</dd></div>
                <div><dt class="text-gray-500">អ្នកជួល / Tenant</dt><dd class="mt-1 font-semibold text-gray-900">{{ $contract->tenant->full_name }}</dd></div>
                <div><dt class="text-gray-500">ទូរស័ព្ទ / Phone</dt><dd class="mt-1 text-gray-900">{{ $contract->tenant->phone }}</dd></div>
                <div><dt class="text-gray-500">អត្តសញ្ញាណប័ណ្ណ / National ID</dt><dd class="mt-1 text-gray-900">{{ $contract->tenant->national_id ?: '—' }}</dd></div>
                <div><dt class="text-gray-500">ទំនាក់ទំនងបន្ទាន់ / Emergency Contact</dt><dd class="mt-1 text-gray-900">{{ $contract->tenant->emergency_contact ?: '—' }}</dd></div>
                <div><dt class="text-gray-500">ប្រាក់កក់ / Deposit</dt><dd class="mt-1 font-semibold text-gray-900">${{ number_format($contract->deposit_amount, 2) }}</dd></div>
                <div><dt class="text-gray-500">ថ្ងៃចាប់ផ្តើម / Start Date</dt><dd class="mt-1 text-gray-900">{{ $contract->start_date->format('Y-m-d') }}</dd></div>
                <div><dt class="text-gray-500">ថ្ងៃបញ្ចប់ / End Date</dt><dd class="mt-1 text-gray-900">{{ $contract->end_date?->format('Y-m-d') ?? '—' }}</dd></div>
                <div>
                    <dt class="text-gray-500">ស្ថានភាព / Status</dt>
                    <dd class="mt-2">
                        @if ($contract->status === 'active')
                            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-800">កំពុងជួល (Active)</span>
                        @else
                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-800">ឈប់ជួល (Ended)</span>
                        @endif
                    </dd>
                </div>
            </dl>

            @if ($contract->status === 'active')
                <form action="{{ route('contracts.checkout', $contract) }}" method="POST" class="mt-6 border-t border-gray-100 pt-5" onsubmit="return confirm('Complete check-out for this tenant?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i> បញ្ចប់ការស្នាក់នៅ / Check-Out
                    </button>
                </form>
            @endif
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold text-gray-900">វិក្កយបត្រ / Invoices</h2>
            @forelse ($contract->invoices as $invoice)
                <div class="mb-3 rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">Invoice {{ $invoice->invoice_date->format('Y-m-d') }}</p>
                            @if ($invoice->meterReading)
                                <p class="mt-1 text-xs text-gray-500">Meter reading: {{ $invoice->meterReading->reading_date->format('Y-m-d') }}</p>
                            @endif
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $invoice->payment_status === 'unpaid' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $invoice->payment_status)) }}
                        </span>
                    </div>
                    <p class="mt-3 text-sm font-bold text-gray-900">${{ number_format($invoice->total_amount, 2) }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">មិនទាន់មានវិក្កយបត្រទេ / No invoices for this contract.</p>
            @endforelse
        </section>
    </div>
</div>
@endsection
