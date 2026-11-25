@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl">
    @if (session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800" role="alert">{{ session('error') }}</div>
    @endif

    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">ចុះកិច្ចសន្យាជួលថ្មី / New Check-In</h1>
            <p class="mt-1 text-sm text-gray-500">Select an available room and enter tenant and contract details.</p>
        </div>
        <a href="{{ route('contracts.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50" title="Back to Contracts" aria-label="Back to Contracts">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    @if ($rooms->isEmpty())
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" role="status">
            មិនមានបន្ទប់ទំនេរទេ / There are no available rooms for check-in.
        </div>
    @endif

    <form action="{{ route('contracts.store') }}" method="POST" class="space-y-6">
        @csrf
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <label for="existing_tenant_id" class="mb-2 block text-sm font-semibold text-gray-700">
                អ្នកជួលចាស់ / Returning Tenant <span class="font-normal text-gray-400">(Leave blank if new)</span>
            </label>
            <select id="existing_tenant_id" name="existing_tenant_id" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option value="">New tenant</option>
                @foreach ($tenants as $tenant)
                    <option value="{{ $tenant->id }}" @selected(old('existing_tenant_id') == $tenant->id)>
                        {{ $tenant->full_name }} — {{ $tenant->phone }}
                    </option>
                @endforeach
            </select>
            @error('existing_tenant_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold text-gray-900">ជ្រើសរើសបន្ទប់ / Select a Room</h2>
            <label for="room_id" class="mb-2 block text-sm font-semibold text-gray-700">បន្ទប់ទំនេរ / Available Room</label>
            <select id="room_id" name="room_id" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" required @disabled($rooms->isEmpty())>
                <option value="">Select an available room</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
                        Room {{ $room->room_number }} — {{ $room->roomType->type_name }} — ${{ number_format($room->roomType->base_price, 2) }}/month
                    </option>
                @endforeach
            </select>
            @error('room_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </section>

        <section id="new-tenant-fields" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm {{ old('existing_tenant_id') ? 'hidden' : '' }}">
            <h2 class="mb-5 text-lg font-bold text-gray-900">ព័ត៌មានអ្នកជួល / Tenant Details</h2>
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="full_name" class="mb-2 block text-sm font-semibold text-gray-700">ឈ្មោះពេញ / Full Name</label>
                    <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" @required(! old('existing_tenant_id')) @disabled(old('existing_tenant_id'))>
                    @error('full_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700">លេខទូរស័ព្ទ / Phone</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" @required(! old('existing_tenant_id')) @disabled(old('existing_tenant_id'))>
                    @error('phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="national_id" class="mb-2 block text-sm font-semibold text-gray-700">អត្តសញ្ញាណប័ណ្ណ / National ID <span class="font-normal text-gray-400">(Required for new tenants)</span></label>
                    <input id="national_id" name="national_id" type="text" value="{{ old('national_id') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" @required(! old('existing_tenant_id')) @disabled(old('existing_tenant_id'))>
                    @error('national_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="emergency_contact" class="mb-2 block text-sm font-semibold text-gray-700">ទំនាក់ទំនងបន្ទាន់ / Emergency Contact <span class="font-normal text-gray-400">(Optional)</span></label>
                    <input id="emergency_contact" name="emergency_contact" type="text" value="{{ old('emergency_contact') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" @disabled(old('existing_tenant_id'))>
                    @error('emergency_contact')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold text-gray-900">ព័ត៌មានកិច្ចសន្យា / Contract Details</h2>
            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <label for="deposit_amount" class="mb-2 block text-sm font-semibold text-gray-700">ប្រាក់កក់ / Deposit ($)</label>
                    <input id="deposit_amount" name="deposit_amount" type="number" min="0" step="0.01" value="{{ old('deposit_amount', '0.00') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" required>
                    @error('deposit_amount')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="start_date" class="mb-2 block text-sm font-semibold text-gray-700">ថ្ងៃចាប់ផ្តើម / Start Date</label>
                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date', now()->toDateString()) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" required>
                    @error('start_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_date" class="mb-2 block text-sm font-semibold text-gray-700">ថ្ងៃបញ្ចប់ / End Date <span class="font-normal text-gray-400">(Optional)</span></label>
                    <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    @error('end_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('contracts.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700" @disabled($rooms->isEmpty())>
                ចុះកិច្ចសន្យា / Complete Check-In
            </button>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tenantSelect = document.getElementById('existing_tenant_id');
        const newTenantFields = document.getElementById('new-tenant-fields');
        const requiredFields = ['full_name', 'phone', 'national_id'].map((id) => document.getElementById(id));
        const tenantFields = [...newTenantFields.querySelectorAll('input')];

        const toggleNewTenantFields = () => {
            const returningTenantSelected = tenantSelect.value !== '';

            newTenantFields.classList.toggle('hidden', returningTenantSelected);
            tenantFields.forEach((field) => {
                field.disabled = returningTenantSelected;
            });
            requiredFields.forEach((field) => {
                field.required = !returningTenantSelected;
            });
        };

        tenantSelect.addEventListener('change', toggleNewTenantFields);
        toggleNewTenantFields();
    });
</script>
@endsection
