@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl">
    @if (session('success'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col justify-between border-b border-gray-200 pb-6 md:flex-row md:items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">អ្នកជួល និងកិច្ចសន្យា / Tenants &amp; Contracts</h1>
            <p class="mt-1 text-sm text-gray-500">Manage tenant check-ins, active contracts, and check-outs.</p>
        </div>
        <a href="{{ route('contracts.create') }}" class="mt-4 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 md:mt-0">
            <i class="fa-solid fa-plus mr-2"></i>
            ចុះកិច្ចសន្យាជួលថ្មី (New Check-In)
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-600">
                        <th class="px-5 py-3">បន្ទប់ / Room</th>
                        <th class="px-5 py-3">អ្នកជួល / Tenant</th>
                        <th class="px-5 py-3">ទូរស័ព្ទ / Phone</th>
                        <th class="px-5 py-3">ប្រាក់កក់ / Deposit</th>
                        <th class="px-5 py-3">ថ្ងៃចាប់ផ្តើម / Start</th>
                        <th class="px-5 py-3">ថ្ងៃបញ្ចប់ / End</th>
                        <th class="px-5 py-3">ស្ថានភាព / Status</th>
                        <th class="px-5 py-3 text-right">សកម្មភាព / Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($contracts as $contract)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $contract->room->room_number }}</td>
                            <td class="px-5 py-4">{{ $contract->tenant->full_name }}</td>
                            <td class="px-5 py-4 text-gray-600">{{ $contract->tenant->phone }}</td>
                            <td class="px-5 py-4 font-medium">${{ number_format($contract->deposit_amount, 2) }}</td>
                            <td class="px-5 py-4">{{ $contract->start_date->format('Y-m-d') }}</td>
                            <td class="px-5 py-4">{{ $contract->end_date?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-5 py-4">
                                @if ($contract->status === 'active')
                                    <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">កំពុងជួល (Active)</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">ឈប់ជួល (Ended)</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('contracts.show', $contract) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50" title="View Contract" aria-label="View Contract">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if ($contract->status === 'active')
                                        <a href="{{ route('contracts.edit', $contract) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100" title="Edit Contract" aria-label="Edit Contract">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('contracts.checkout', $contract) }}" method="POST" onsubmit="return confirm('Complete check-out for this tenant?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex h-8 items-center justify-center rounded-md border border-amber-200 bg-amber-50 px-3 text-xs font-semibold text-amber-800 hover:bg-amber-100" title="Check Out">
                                                <i class="fa-solid fa-right-from-bracket mr-1.5"></i> Check-Out
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-gray-500">មិនទាន់មានកិច្ចសន្យាទេ / No contracts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
