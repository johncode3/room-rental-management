@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">បន្ថែមបន្ទប់ / Add Room</h1>
            <p class="mt-1 text-sm text-gray-500">Create a room and assign it to a room type.</p>
        </div>
        <a href="{{ route('rooms.index') }}"
           class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 shadow-sm transition hover:bg-gray-50 hover:text-indigo-600"
           title="Back to Rooms"
           aria-label="Back to Rooms">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <form action="{{ route('rooms.store') }}" method="POST" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        @csrf

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="room_number" class="mb-2 block text-sm font-semibold text-gray-700">លេខបន្ទប់ / Room Number</label>
                <input id="room_number" name="room_number" type="text" value="{{ old('room_number') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                       required>
                @error('room_number')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="room_type_id" class="mb-2 block text-sm font-semibold text-gray-700">ប្រភេទបន្ទប់ / Room Type</label>
                <select id="room_type_id" name="room_type_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                        required>
                    <option value="">Select room type</option>
                    @foreach ($roomTypes as $roomType)
                        <option value="{{ $roomType->id }}" @selected(old('room_type_id') == $roomType->id)>
                            {{ $roomType->type_name }} - ${{ number_format($roomType->base_price, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('room_type_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">ស្ថានភាព / Status</label>
                <select id="status" name="status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                        required>
                    <option value="available" @selected(old('status', 'available') === 'available')>ទំនេរ (Available)</option>
                    <option value="occupied" @selected(old('status') === 'occupied')>មានអ្នកជួល (Occupied)</option>
                    <option value="maintenance" @selected(old('status') === 'maintenance')>កំពុងជួសជុល (Maintenance)</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('rooms.index') }}"
               class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50"
               title="Cancel"
               aria-label="Cancel">
                <i class="fa-solid fa-xmark"></i>
            </a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">បន្ថែម / Add Room</button>
        </div>
    </form>
</div>
@endsection
