@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
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

    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-gray-200 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Room Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage rooms, pricing, and live occupancy status.</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <span class="bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm">
                Total Rooms: {{ $rooms->count() }}
            </span>
            <a href="{{ route('rooms.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <i class="fa-solid fa-plus mr-2"></i>
                បន្ថែមបន្ទប់ថ្មី (Add Room)
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold uppercase tracking-wider text-gray-600">
                        <th class="py-3.5 px-6">លេខបន្ទប់ / Room</th>
                        <th class="py-3.5 px-6">ប្រភេទបន្ទប់ / Type</th>
                        <th class="py-3.5 px-6">តម្លៃជួល / Price</th>
                        <th class="py-3.5 px-6">ស្ថានភាព / Status</th>
                        <th class="py-3.5 px-6 text-right">សកម្មភាព / Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($rooms as $room)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="py-4 px-6 font-bold text-gray-900 flex items-center space-x-2">
                                <i class="fa-solid fa-door-closed text-gray-400 text-xs"></i>
                                <span>Room {{ $room->room_number }}</span>
                            </td>
                            <td class="py-4 px-6 text-gray-600">
                                {{ $room->roomType->type_name ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-gray-900">
                                ${{ number_format($room->roomType->base_price ?? 0, 2) }}
                                <span class="text-xs text-gray-400 font-normal">/mo</span>
                            </td>
                            <td class="py-4 px-6">
                                @if($room->status === 'available')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span><span class="font-khmer">ទំនេរ (Available)</span>
                                    </span>
                                @elseif($room->status === 'occupied')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span><span class="font-khmer">មានអ្នកជួល (Occupied)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span><span class="font-khmer">កំពុងជួសជុល (Maintenance)</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex justify-end items-center space-x-2">
                                    <a href="{{ route('rooms.show', $room) }}"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                                       title="View Room"
                                       aria-label="View Room">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('rooms.edit', $room) }}"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100"
                                       title="Edit Room"
                                       aria-label="Edit Room">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this room?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-red-200 bg-red-50 text-red-700 hover:bg-red-100"
                                                title="Delete Room"
                                                aria-label="Delete Room">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-400">
                                No rooms found. Run your seeder to view records.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection