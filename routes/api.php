<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('devices', DeviceController::class);
});
