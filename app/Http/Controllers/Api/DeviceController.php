<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use Illuminate\Http\Response;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return DeviceResource::collection($devices);
    }

    public function store(StoreDeviceRequest $request)
    {
        $user = $request->user(); // user z tokena (Sanctum)
    
        $device = $user->devices()->create($request->validated());
    
        return response()->json($device, 201);
    }
    

    public function show(Device $device)
    {
        $this->ensureOwner($device);

        return new DeviceResource($device);
    }

    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $this->ensureOwner($device);

        $device->update($request->validated());

        return new DeviceResource($device);
    }

    public function destroy(Device $device)
    {
        $this->ensureOwner($device);

        $device->delete();

        return response()->noContent();
    }

    private function ensureOwner(Device $device): void
    {
        abort_if($device->user_id !== auth()->id(), Response::HTTP_FORBIDDEN);
    }
}
