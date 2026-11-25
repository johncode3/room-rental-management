<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::with('roomType')->latest()->get();

        return view('rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        $roomTypes = RoomType::orderBy('type_name')->get();

        return view('rooms.create', compact('roomTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:255', 'unique:rooms,room_number'],
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'status' => ['required', 'in:available,occupied,maintenance'],
        ]);

        Room::create($validated);

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room added successfully.');
    }

    public function show(Room $room): View
    {
        $room->load([
            'roomType',
            'contracts' => fn ($query) => $query
                ->where('status', 'active')
                ->latest('start_date')
                ->with('tenant'),
        ]);

        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        $roomTypes = RoomType::orderBy('type_name')->get();

        return view('rooms.edit', compact('room', 'roomTypes'));
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:255', 'unique:rooms,room_number,' . $room->id],
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'status' => ['required', 'in:available,occupied,maintenance'],
        ]);

        $room->update($validated);

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->status === 'occupied') {
            return redirect()
                ->back()
                ->with('error', 'មិនអាចលុបបន្ទប់ដែលកំពុងមានអ្នកជួលស្នាក់នៅបានទេ (Cannot delete an occupied room).');
        }

        $room->delete();

        return redirect()
            ->route('rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}
