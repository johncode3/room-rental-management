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

        $standard = RoomType::updateOrCreate(['type_name' => 'Standard Room'], [
            'base_price' => 60,
            'description' => 'Comfortable furnished room for one or two people.',
        ]);
        $deluxe = RoomType::updateOrCreate(['type_name' => 'Deluxe Room'], [
            'base_price' => 100,
            'description' => 'Larger furnished room with private bathroom.',
        ]);
        $studio = RoomType::updateOrCreate(['type_name' => 'Studio Apartment'], [
            'base_price' => 150,
            'description' => 'Self-contained studio for long-term tenants.',
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
            ['full_name' => 'Sokha Chan', 'phone' => '012 345 678', 'national_id' => 'KH-10001', 'emergency_contact' => 'Chan Dara - 012 222 333'],
            ['full_name' => 'Dara Sreyneang', 'phone' => '097 456 789', 'national_id' => 'KH-10002', 'emergency_contact' => 'Srey Mom - 097 111 222'],
            ['full_name' => 'Vannak Kim', 'phone' => '010 987 654', 'national_id' => 'KH-10003', 'emergency_contact' => 'Kim Sothea - 010 333 444'],
            ['full_name' => 'Pisey Ly', 'phone' => '088 222 111', 'national_id' => 'KH-10004', 'emergency_contact' => 'Ly Bopha - 088 555 666'],
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
