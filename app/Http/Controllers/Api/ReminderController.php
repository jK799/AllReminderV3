<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReminderRequest;
use App\Http\Requests\UpdateReminderRequest;
use App\Models\Device;
use App\Models\Reminder;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $items = Reminder::query()
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();

        return response()->json($items);
    }

    public function store(StoreReminderRequest $request)
    {
        $userId = $request->user()->id;

        $deviceId  = $request->input('device_id');
        $vehicleId = $request->input('vehicle_id');

        // ma być albo device, albo vehicle, albo nic
        if (!is_null($deviceId) && !is_null($vehicleId)) {
            return response()->json([
                'message' => 'Podaj albo device_id, albo vehicle_id (nie oba naraz).',
            ], 422);
        }

        // jeśli device_id podany -> musi należeć do usera
        if (!is_null($deviceId)) {
            $deviceOk = Device::query()
                ->where('id', $deviceId)
                ->where('user_id', $userId)
                ->exists();

            if (!$deviceOk) {
                return response()->json([
                    'message' => 'device_id jest nieprawidłowe albo nie należy do zalogowanego użytkownika.',
                ], 422);
            }
        }

        // jeśli vehicle_id podany -> musi należeć do usera
        if (!is_null($vehicleId)) {
            $vehicleOk = Vehicle::query()
                ->where('id', $vehicleId)
                ->where('user_id', $userId)
                ->exists();

            if (!$vehicleOk) {
                return response()->json([
                    'message' => 'vehicle_id jest nieprawidłowe albo nie należy do zalogowanego użytkownika.',
                ], 422);
            }
        }

        $data = $request->validated();

        // nigdy nie ufamy user_id z requestu
        $data['user_id'] = $userId;

        // domyślnie aktywne
        if (!array_key_exists('is_active', $data)) {
            $data['is_active'] = true;
        }

        $reminder = Reminder::create($data);

        return response()->json($reminder, 201);
    }

    public function show(Request $request, Reminder $reminder)
    {
        // scoping po użytkowniku
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($reminder);
    }

    public function update(UpdateReminderRequest $request, Reminder $reminder)
    {
        $userId = $request->user()->id;

        if ($reminder->user_id !== $userId) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $deviceId  = $request->input('device_id', $reminder->device_id);
        $vehicleId = $request->input('vehicle_id', $reminder->vehicle_id);

        if (!is_null($deviceId) && !is_null($vehicleId)) {
            return response()->json([
                'message' => 'Podaj albo device_id, albo vehicle_id (nie oba naraz).',
            ], 422);
        }

        if (!is_null($deviceId)) {
            $deviceOk = Device::query()
                ->where('id', $deviceId)
                ->where('user_id', $userId)
                ->exists();

            if (!$deviceOk) {
                return response()->json([
                    'message' => 'device_id jest nieprawidłowe albo nie należy do zalogowanego użytkownika.',
                ], 422);
            }
        }

        if (!is_null($vehicleId)) {
            $vehicleOk = Vehicle::query()
                ->where('id', $vehicleId)
                ->where('user_id', $userId)
                ->exists();

            if (!$vehicleOk) {
                return response()->json([
                    'message' => 'vehicle_id jest nieprawidłowe albo nie należy do zalogowanego użytkownika.',
                ], 422);
            }
        }

        $data = $request->validated();

        // zabezpieczenie: user_id nigdy nie zmieniamy z requestu
        unset($data['user_id']);

        $reminder->update($data);

        return response()->json($reminder);
    }

    public function destroy(Request $request, Reminder $reminder)
    {
        if ($reminder->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $reminder->delete();

        return response()->json(null, 204);
    }
}
