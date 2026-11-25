<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ContractController;

Route::get('/', function () {
    return redirect()->route('rooms.index');
});

Route::resource('rooms', RoomController::class);

Route::resource('contracts', ContractController::class)->only([
    'index',
    'create',
    'store',
    'show',
    'edit',
    'update',
]);
Route::patch('/contracts/{contract}/checkout', [ContractController::class, 'checkout'])
    ->name('contracts.checkout');
