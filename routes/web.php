<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;

Route::get('/', function () {
    return redirect()->route('rooms.index');
});

Route::resource('rooms', RoomController::class);
