<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;

Route::get('/ping', fn () => response()->json(['ok' => true]));

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('devices', \App\Http\Controllers\Api\DeviceController::class);
    Route::get('me', fn (\Illuminate\Http\Request $r) => $r->user());
});

