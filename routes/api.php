<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\VehicleController;

Route::get('/ping', fn () => response()->json(['ok' => true]));

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', fn (\Illuminate\Http\Request $r) => $r->user());

    Route::apiResource('devices', DeviceController::class);
    Route::apiResource('vehicles', VehicleController::class);
});
