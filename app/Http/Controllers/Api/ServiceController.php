<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();

        return response()->json($services);
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();

        // device XOR vehicle (albo jedno, albo drugie, albo oba null)
        if (!empty($data['device_id']) && !empty($data['vehicle_id'])) {
            return response()->json([
                'message' => 'Wybierz albo device_id albo vehicle_id (nie oba naraz).'
            ], 422);
        }

        // Opcjonalnie: upewnij się, że device/vehicle należy do użytkownika
        if (!empty($data['device_id'])) {
            $ownsDevice = $request->user()->devices()->where('id', $data['device_id'])->exists();
            if (!$ownsDevice) {
                return response()->json(['message' => 'Ten device nie należy do zalogowanego użytkownika.'], 403);
            }
        }

        if (!empty($data['vehicle_id'])) {
            $ownsVehicle = $request->user()->vehicles()->where('id', $data['vehicle_id'])->exists();
            if (!$ownsVehicle) {
                return response()->json(['message' => 'Ten vehicle nie należy do zalogowanego użytkownika.'], 403);
            }
        }

        $service = Service::create([
            ...$data,
            'user_id' => $request->user()->id,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json($service, 201);
    }

    public function show(Request $request, Service $service)
    {
        if ($service->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json($service);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        if ($service->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $data = $request->validated();

        // device XOR vehicle (albo jedno, albo drugie, albo oba null)
        $deviceId = $data['device_id'] ?? $service->device_id;
        $vehicleId = $data['vehicle_id'] ?? $service->vehicle_id;

        if (!empty($deviceId) && !empty($vehicleId)) {
            return response()->json([
                'message' => 'Wybierz albo device_id albo vehicle_id (nie oba naraz).'
            ], 422);
        }

        // Sprawdzenie własności jeżeli przyszło w payloadzie
        if (array_key_exists('device_id', $data) && !empty($data['device_id'])) {
            $ownsDevice = $request->user()->devices()->where('id', $data['device_id'])->exists();
            if (!$ownsDevice) {
                return response()->json(['message' => 'Ten device nie należy do zalogowanego użytkownika.'], 403);
            }
        }

        if (array_key_exists('vehicle_id', $data) && !empty($data['vehicle_id'])) {
            $ownsVehicle = $request->user()->vehicles()->where('id', $data['vehicle_id'])->exists();
            if (!$ownsVehicle) {
                return response()->json(['message' => 'Ten vehicle nie należy do zalogowanego użytkownika.'], 403);
            }
        }

        $service->update($data);

        return response()->json($service);
    }

    public function destroy(Request $request, Service $service)
    {
        if ($service->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $service->delete();

        return response()->json(null, 204);
    }
}
