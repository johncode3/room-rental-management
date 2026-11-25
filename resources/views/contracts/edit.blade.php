@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">កែប្រែអ្នកជួល និងកិច្ចសន្យា / Edit Tenant &amp; Contract</h1>
            <p class="mt-1 text-sm text-gray-500">Update tenant details and contract dates or deposit.</p>
        </div>
        <a href="{{ route('contracts.show', $contract) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50" title="Back to Contract" aria-label="Back to Contract">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <form action="{{ route('contracts.update', $contract) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold text-gray-900">បន្ទប់ / Room (Read Only)</h2>
            <div class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-700">
                <span class="font-semibold">Room {{ $contract->room->room_number }}</span>
                <span class="mx-1 text-gray-400">·</span>
                {{ $contract->room->roomType->type_name }}
                <span class="mx-1 text-gray-400">·</span>
                ${{ number_format($contract->room->roomType->base_price, 2) }}/month
            </div>
            <p class="mt-2 text-xs text-gray-500">Room and occupancy status cannot be changed here.</p>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold text-gray-900">ព័ត៌មានអ្នកជួល / Tenant Details</h2>
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="full_name" class="mb-2 block text-sm font-semibold text-gray-700">ឈ្មោះពេញ / Full Name</label>
                    <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $contract->tenant->full_name) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" required>
                    @error('full_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700">លេខទូរស័ព្ទ / Phone</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone', $contract->tenant->phone) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" required>
                    @error('phone')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="national_id" class="mb-2 block text-sm font-semibold text-gray-700">អត្តសញ្ញាណប័ណ្ណ / National ID <span class="font-normal text-gray-400">(Optional)</span></label>
                    <input id="national_id" name="national_id" type="text" value="{{ old('national_id', $contract->tenant->national_id) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    @error('national_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="emergency_contact" class="mb-2 block text-sm font-semibold text-gray-700">ទំនាក់ទំនងបន្ទាន់ / Emergency Contact <span class="font-normal text-gray-400">(Optional)</span></label>
                    <input id="emergency_contact" name="emergency_contact" type="text" value="{{ old('emergency_contact', $contract->tenant->emergency_contact) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    @error('emergency_contact')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-5 text-lg font-bold text-gray-900">ព័ត៌មានកិច្ចសន្យា / Contract Details</h2>
            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <label for="start_date" class="mb-2 block text-sm font-semibold text-gray-700">ថ្ងៃចាប់ផ្តើម / Start Date</label>
                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $contract->start_date->toDateString()) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" required>
                    @error('start_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_date" class="mb-2 block text-sm font-semibold text-gray-700">ថ្ងៃបញ្ចប់ / End Date <span class="font-normal text-gray-400">(Optional)</span></label>
                    <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $contract->end_date?->toDateString()) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    @error('end_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="deposit_amount" class="mb-2 block text-sm font-semibold text-gray-700">ប្រាក់កក់ / Deposit ($)</label>
                    <input id="deposit_amount" name="deposit_amount" type="number" min="0" step="0.01" value="{{ old('deposit_amount', $contract->deposit_amount) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100" required>
                    @error('deposit_amount')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('contracts.show', $contract) }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                រក្សាទុក / Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
