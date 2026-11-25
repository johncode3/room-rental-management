<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\MeterReading;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoomRentalSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'System Administrator',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $standard = RoomType::updateOrCreate(['type_name' => 'បន្ទប់កង្ហារ (ធម្មតា)'], [
            'base_price' => 45,
            'description' => 'មានកង្ហារ គ្រែ១ និងបន្ទប់ទឹកក្នុង',
        ]);
        $deluxe = RoomType::updateOrCreate(['type_name' => 'បន្ទប់ម៉ាស៊ីនត្រជាក់ (គ្រែ១)'], [
            'base_price' => 80,
            'description' => 'មានម៉ាស៊ីនត្រជាក់ គ្រែធំ និងទូសម្លៀកបំពាក់',
        ]);
        $studio = RoomType::updateOrCreate(['type_name' => 'បន្ទប់ម៉ាស៊ីនត្រជាក់ (ធំ + ផ្ទះបាយ)'], [
            'base_price' => 120,
            'description' => 'បន្ទប់ធំទូលាយ មានម៉ាស៊ីនត្រជាក់ ផ្ទះបាយ និងវេរ៉ង់ដា',
        ]);

        $rooms = collect([
            ['room_type_id' => $standard->id, 'room_number' => '101', 'status' => 'occupied'],
            ['room_type_id' => $standard->id, 'room_number' => '102', 'status' => 'occupied'],
            ['room_type_id' => $standard->id, 'room_number' => '103', 'status' => 'available'],
            ['room_type_id' => $deluxe->id, 'room_number' => '201', 'status' => 'occupied'],
            ['room_type_id' => $deluxe->id, 'room_number' => '202', 'status' => 'maintenance'],
            ['room_type_id' => $studio->id, 'room_number' => '301', 'status' => 'occupied'],
        ])->mapWithKeys(function (array $room): array {
            return [$room['room_number'] => Room::updateOrCreate(
                ['room_number' => $room['room_number']],
                $room,
            )];
        });

        $tenants = collect([
            ['full_name' => 'សុខ ដារ៉ា', 'phone' => '012 345 678', 'national_id' => 'KH-10001', 'emergency_contact' => 'ដារ៉ា ធារា - 012 222 333'],
            ['full_name' => 'ចាន់ ស្រីនិច', 'phone' => '097 456 789', 'national_id' => 'KH-10002', 'emergency_contact' => 'ស្រីមុំ - 097 111 222'],
            ['full_name' => 'កែវ វិបុល', 'phone' => '010 987 654', 'national_id' => 'KH-10003', 'emergency_contact' => 'កែវ សុធា - 010 333 444'],
            ['full_name' => 'លី សុជាតា', 'phone' => '088 222 111', 'national_id' => 'KH-10004', 'emergency_contact' => 'លី បូពា - 088 555 666'],
        ])->map(fn (array $tenant): Tenant => Tenant::updateOrCreate(
            ['national_id' => $tenant['national_id']],
            $tenant,
        ));

        $contracts = collect([
            [$tenants[0], $rooms['101'], 120],
            [$tenants[1], $rooms['102'], 120],
            [$tenants[2], $rooms['201'], 200],
            [$tenants[3], $rooms['301'], 300],
        ])->map(fn (array $data): Contract => Contract::updateOrCreate([
            'tenant_id' => $data[0]->id,
            'room_id' => $data[1]->id,
            'start_date' => '2026-01-01',
        ], [
            'end_date' => null,
            'deposit_amount' => $data[2],
            'status' => 'active',
        ]));

        foreach ($contracts as $index => $contract) {
            $reading = MeterReading::updateOrCreate([
                'contract_id' => $contract->id,
                'reading_date' => '2026-10-01',
            ], [
                'old_electric' => 100 + ($index * 80),
                'new_electric' => 145 + ($index * 80),
                'old_water' => 20 + ($index * 10),
                'new_water' => 25 + ($index * 10),
            ]);

            $rent = (float) $contract->room->roomType->base_price;
            $electricity = ((float) $reading->new_electric - (float) $reading->old_electric) * 0.25;
            $water = ((float) $reading->new_water - (float) $reading->old_water) * 0.50;
            $invoice = Invoice::updateOrCreate([
                'contract_id' => $contract->id,
                'meter_reading_id' => $reading->id,
                'invoice_date' => '2026-10-01',
            ], [
                'total_amount' => $rent + $electricity + $water,
                'payment_status' => $index < 3 ? 'paid' : 'unpaid',
            ]);

            if ($index < 3) {
                Payment::updateOrCreate([
                    'invoice_id' => $invoice->id,
                    'payment_date' => '2026-10-03',
                ], [
                    'amount_paid' => $invoice->total_amount,
                    'payment_method' => $index === 0 ? 'cash' : 'aba_pay',
                ]);
            }
        }
    }
}
