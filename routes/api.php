<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\ServiceController;


Route::get('/ping', fn () => response()->json(['ok' => true]));

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', fn (\Illuminate\Http\Request $r) => $r->user());

    Route::apiResource('devices', DeviceController::class);
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('reminders', ReminderController::class);
    Route::apiResource('services', ServiceController::class);

});
