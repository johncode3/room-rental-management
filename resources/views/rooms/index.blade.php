<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Rental Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8 font-sans">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Room Management</h1>
                <p class="text-sm text-gray-500">Overview of all rental rooms</p>
            </div>
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                Total Rooms: {{ $rooms->count() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-gray-50 text-gray-600 text-sm">
                        <th class="py-3 px-4">Room No.</th>
                        <th class="py-3 px-4">Room Type</th>
                        <th class="py-3 px-4">Base Price</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse ($rooms as $room)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-bold text-gray-800">{{ $room->room_number }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $room->roomType->type_name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-gray-600">${{ number_format($room->roomType->base_price ?? 0, 2) }}</td>
                            <td class="py-3 px-4">
                                @if($room->status === 'available')
                                    <span class="bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded font-medium">Available</span>
                                @elseif($room->status === 'occupied')
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded font-medium">Occupied</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-0.5 rounded font-medium">{{ ucfirst($room->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">No rooms found. Run the seeder first!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>