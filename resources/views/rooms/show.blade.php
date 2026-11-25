@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">ព័ត៌មានបន្ទប់ / Room Details</h1>
            <p class="mt-1 text-sm text-gray-500">Room {{ $room->room_number }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('rooms.edit', $room) }}"
               class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100"
               title="Edit Room"
               aria-label="Edit Room">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
            <a href="{{ route('rooms.index') }}"
               class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50"
               title="Back to Rooms"
               aria-label="Back to Rooms">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-gray-500">បន្ទប់ / Room Information</h2>
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-gray-500">លេខបន្ទប់ / Room Number</dt>
                    <dd class="mt-1 font-bold text-gray-900">{{ $room->room_number }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">ប្រភេទបន្ទប់ / Room Type</dt>
                    <dd class="mt-1 font-semibold text-gray-900">{{ $room->roomType->type_name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">តម្លៃជួល / Base Price</dt>
                    <dd class="mt-1 font-semibold text-gray-900">${{ number_format($room->roomType->base_price, 2) }} /mo</dd>
                </div>
                <div>
                    <dt class="text-gray-500">ស្ថានភាព / Status</dt>
                    <dd class="mt-2">
                        @if ($room->status === 'available')
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800">ទំនេរ (Available)</span>
                        @elseif ($room->status === 'occupied')
                            <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-800">មានអ្នកជួល (Occupied)</span>
                        @else
                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">កំពុងជួសជុល (Maintenance)</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-gray-500">អ្នកជួលបច្ចុប្បន្ន / Active Tenant</h2>
            @forelse ($room->contracts as $contract)
                <div class="mb-3 rounded-lg border border-gray-100 bg-gray-50 p-4 text-sm">
                    <p class="font-bold text-gray-900">{{ $contract->tenant->full_name }}</p>
                    <p class="mt-1 text-gray-600">{{ $contract->tenant->phone }}</p>
                    <p class="mt-2 text-xs text-gray-500">Contract: {{ $contract->start_date->format('Y-m-d') }} → {{ $contract->end_date?->format('Y-m-d') ?? 'Open-ended' }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">No active contract.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
