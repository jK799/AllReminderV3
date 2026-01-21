<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        return Vehicle::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get();
    }

    public function store(StoreVehicleRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $vehicle = Vehicle::create($data);

        return response()->json($vehicle, 201);
    }

    public function show(Request $request, Vehicle $vehicle)
    {
        abort_unless($vehicle->user_id === $request->user()->id, 403);

        return $vehicle;
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        abort_unless($vehicle->user_id === $request->user()->id, 403);

        $vehicle->update($request->validated());

        return response()->json($vehicle, 200);
    }

    public function destroy(Request $request, Vehicle $vehicle)
    {
        abort_unless($vehicle->user_id === $request->user()->id, 403);

        $vehicle->delete();

        return response()->json(['deleted' => true], 200);
    }
}
