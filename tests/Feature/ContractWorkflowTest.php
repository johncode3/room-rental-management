<?php

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\MeterReading;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeRoom(string $status = 'available'): Room
{
    $roomType = RoomType::create([
        'type_name' => 'បន្ទប់សាកល្បង',
        'base_price' => 45,
    ]);

    return Room::create([
        'room_number' => 'T-101',
        'room_type_id' => $roomType->id,
        'status' => $status,
    ]);
}

function makeContract(Room $room): Contract
{
    $tenant = Tenant::create([
        'full_name' => 'សុខ ដារ៉ា',
        'phone' => '012345678',
    ]);

    return Contract::create([
        'tenant_id' => $tenant->id,
        'room_id' => $room->id,
        'start_date' => '2026-01-01',
        'deposit_amount' => 45,
        'status' => 'active',
    ]);
}

test('check-in creates tenant and contract and marks the room occupied', function () {
    $room = makeRoom();

    $response = $this->post(route('contracts.store'), [
        'full_name' => 'សុខ ដារ៉ា',
        'phone' => '012345678',
        'national_id' => 'KH-TEST-1',
        'emergency_contact' => '012222333',
        'room_id' => $room->id,
        'start_date' => '2026-11-25',
        'deposit_amount' => '45.00',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tenants', ['national_id' => 'KH-TEST-1']);
    $this->assertDatabaseHas('contracts', [
        'room_id' => $room->id,
        'status' => 'active',
        'deposit_amount' => '45.00',
    ]);
    $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'occupied']);
});

test('check-in reuses an existing tenant and creates another contract', function () {
    $previousRoom = makeRoom();
    $existingTenant = Tenant::create([
        'full_name' => 'សុខ ដារ៉ា',
        'phone' => '012345678',
        'national_id' => 'KH-RETURNING',
    ]);
    Contract::create([
        'tenant_id' => $existingTenant->id,
        'room_id' => $previousRoom->id,
        'start_date' => '2025-01-01',
        'end_date' => '2025-12-31',
        'deposit_amount' => 45,
        'status' => 'ended',
    ]);

    $roomType = RoomType::create([
        'type_name' => 'បន្ទប់ទីពីរ',
        'base_price' => 80,
    ]);
    $newRoom = Room::create([
        'room_number' => 'T-102',
        'room_type_id' => $roomType->id,
        'status' => 'available',
    ]);

    $response = $this->post(route('contracts.store'), [
        'existing_tenant_id' => $existingTenant->id,
        'room_id' => $newRoom->id,
        'start_date' => '2026-11-25',
        'deposit_amount' => '80.00',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseCount('tenants', 1);
    $this->assertDatabaseCount('contracts', 2);
    $this->assertDatabaseHas('contracts', [
        'tenant_id' => $existingTenant->id,
        'room_id' => $newRoom->id,
        'status' => 'active',
    ]);
    $this->assertDatabaseHas('rooms', ['id' => $newRoom->id, 'status' => 'occupied']);
});

test('active tenants are omitted from returning tenant options and cannot receive another active contract', function () {
    $currentRoom = makeRoom('occupied');
    $activeTenant = Tenant::create([
        'full_name' => 'សុខ ដារ៉ា',
        'phone' => '012345678',
    ]);
    Contract::create([
        'tenant_id' => $activeTenant->id,
        'room_id' => $currentRoom->id,
        'start_date' => '2026-01-01',
        'deposit_amount' => 45,
        'status' => 'active',
    ]);

    $roomType = RoomType::create([
        'type_name' => 'បន្ទប់ថ្មី',
        'base_price' => 80,
    ]);
    $availableRoom = Room::create([
        'room_number' => 'T-102',
        'room_type_id' => $roomType->id,
        'status' => 'available',
    ]);

    $this->get(route('contracts.create'))
        ->assertOk()
        ->assertDontSee('សុខ ដារ៉ា — 012345678');

    $response = $this->from(route('contracts.create'))->post(route('contracts.store'), [
        'existing_tenant_id' => $activeTenant->id,
        'room_id' => $availableRoom->id,
        'start_date' => '2026-11-25',
        'deposit_amount' => '80.00',
    ]);

    $response->assertRedirect(route('contracts.create'));
    $response->assertSessionHasErrors([
        'existing_tenant_id' => 'អ្នកជួលនេះកំពុងមានកិច្ចសន្យាសកម្មរួចហើយ (This tenant already has an active contract).',
    ]);
    $this->assertDatabaseCount('contracts', 1);
    $this->assertDatabaseHas('rooms', ['id' => $availableRoom->id, 'status' => 'available']);
});

test('new tenant check-in rejects a phone number already in use', function () {
    $room = makeRoom();
    Tenant::create([
        'full_name' => 'ចាន់ ស្រីនិច',
        'phone' => '012345678',
    ]);

    $response = $this->from(route('contracts.create'))->post(route('contracts.store'), [
        'full_name' => 'កែវ វិបុល',
        'phone' => '012345678',
        'national_id' => 'KH-DUPLICATE-PHONE',
        'room_id' => $room->id,
        'start_date' => '2026-11-25',
        'deposit_amount' => '45.00',
    ]);

    $response->assertRedirect(route('contracts.create'));
    $response->assertSessionHasErrors('phone');
    $this->assertDatabaseCount('tenants', 1);
    $this->assertDatabaseCount('contracts', 0);
    $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'available']);
});

test('checkout is blocked while the contract has an unpaid invoice', function () {
    $room = makeRoom('occupied');
    $contract = makeContract($room);
    $reading = MeterReading::create([
        'contract_id' => $contract->id,
        'reading_date' => '2026-11-01',
        'new_electric' => 10,
        'new_water' => 5,
    ]);
    Invoice::create([
        'contract_id' => $contract->id,
        'meter_reading_id' => $reading->id,
        'invoice_date' => '2026-11-01',
        'total_amount' => 45,
        'payment_status' => 'unpaid',
    ]);

    $response = $this->patch(route('contracts.checkout', $contract));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'មិនអាចបញ្ចប់កិច្ចសន្យាបានទេ ព្រោះអ្នកជួលនៅជំពាក់វិក្កយបត្រ (Cannot checkout with unpaid invoices).');
    $this->assertDatabaseHas('contracts', ['id' => $contract->id, 'status' => 'active']);
    $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'occupied']);
});

test('checkout ends the contract and makes the room available when no unpaid invoices exist', function () {
    $room = makeRoom('occupied');
    $contract = makeContract($room);

    $response = $this->patch(route('contracts.checkout', $contract));

    $response->assertRedirect(route('contracts.show', $contract));
    $this->assertDatabaseHas('contracts', ['id' => $contract->id, 'status' => 'ended']);
    $this->assertSame(now()->toDateString(), $contract->fresh()->end_date->toDateString());
    $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'available']);
});

test('active contract can update tenant and safe contract fields without changing its room or status', function () {
    $room = makeRoom('occupied');
    $contract = makeContract($room);
    $contract->tenant->update(['national_id' => 'KH-KEEP']);

    $this->get(route('contracts.edit', $contract))
        ->assertOk()
        ->assertSee('Room T-101')
        ->assertDontSee('name="room_id"', false)
        ->assertDontSee('name="status"', false);

    $response = $this->put(route('contracts.update', $contract), [
        'full_name' => 'ចាន់ ស្រីនិច',
        'phone' => '098765432',
        'national_id' => 'KH-KEEP',
        'emergency_contact' => '012222333',
        'start_date' => '2026-02-01',
        'end_date' => '2026-12-31',
        'deposit_amount' => '80.00',
        'room_id' => 999,
        'status' => 'ended',
    ]);

    $response->assertRedirect(route('contracts.show', $contract));
    $this->assertDatabaseHas('tenants', [
        'id' => $contract->tenant_id,
        'full_name' => 'ចាន់ ស្រីនិច',
        'phone' => '098765432',
        'national_id' => 'KH-KEEP',
    ]);
    $this->assertDatabaseHas('contracts', [
        'id' => $contract->id,
        'room_id' => $room->id,
        'status' => 'active',
        'deposit_amount' => 80,
    ]);
    $updatedContract = $contract->fresh();
    $this->assertSame('2026-02-01', $updatedContract->start_date->toDateString());
    $this->assertSame('2026-12-31', $updatedContract->end_date->toDateString());
});
