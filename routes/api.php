<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\DocumentController;

Route::get('/ping', fn () => response()->json(['ok' => true]));

// AUTH (public)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// AUTH (protected)
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });

    Route::get('me', fn (\Illuminate\Http\Request $r) => $r->user());

    Route::apiResource('devices', DeviceController::class);
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('reminders', ReminderController::class);
    Route::apiResource('services', ServiceController::class);

    Route::post('documents/upload', [DocumentController::class, 'store']);
    Route::get('documents', [DocumentController::class, 'index']);
    Route::delete('documents/{document}', [DocumentController::class, 'destroy']);
});
