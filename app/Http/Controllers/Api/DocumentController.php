<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Document;
use App\Models\Documentable;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        return Document::where('user_id', $request->user()->id)
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:jpg,jpeg,png,webp,pdf'],
            'title' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],

            // category: vehicles / devices / general
            'category' => ['required', Rule::in(['vehicles', 'devices', 'general'])],

            // IDki pod przypięcie (opcjonalne na poziomie typów)
            'vehicle_id' => ['nullable', 'integer'],
            'device_id' => ['nullable', 'integer'],
        ]);

        $user = $request->user();

        // WYMUSZENIE: tylko jedna opcja (vehicle / device / general)
        if ($data['category'] === 'vehicles') {
            if (empty($data['vehicle_id']) || !empty($data['device_id'])) {
                return response()->json([
                    'message' => 'Dla category=vehicles wymagane jest vehicle_id, a device_id musi być puste.'
                ], 422);
            }
        }

        if ($data['category'] === 'devices') {
            if (empty($data['device_id']) || !empty($data['vehicle_id'])) {
                return response()->json([
                    'message' => 'Dla category=devices wymagane jest device_id, a vehicle_id musi być puste.'
                ], 422);
            }
        }

        if ($data['category'] === 'general') {
            if (!empty($data['device_id']) || !empty($data['vehicle_id'])) {
                return response()->json([
                    'message' => 'Dla category=general vehicle_id i device_id muszą być puste.'
                ], 422);
            }
        }

        // Opcjonalnie: sprawdzenie czy vehicle/device należy do usera (bez tego ktoś mógłby przypiąć do cudzego ID)
        if ($data['category'] === 'vehicles') {
            $vehicle = Vehicle::where('id', $data['vehicle_id'])->where('user_id', $user->id)->first();
            if (!$vehicle) {
                return response()->json(['message' => 'Nie znaleziono pojazdu dla tego użytkownika.'], 404);
            }
        }

        if ($data['category'] === 'devices') {
            $device = Device::where('id', $data['device_id'])->where('user_id', $user->id)->first();
            if (!$device) {
                return response()->json(['message' => 'Nie znaleziono urządzenia dla tego użytkownika.'], 404);
            }
        }

        $file = $data['file'];

        // Zapis pliku (storage/app/documents/{user_id}/...)
        $path = $file->store('documents/' . $user->id, 'local');

        $document = Document::create([
            'user_id' => $user->id,
            'title' => $data['title'] ?? null,
            'notes' => $data['notes'] ?? null,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        // Powiązanie tylko jeśli vehicles/devices
        if ($data['category'] === 'vehicles') {
            Documentable::create([
                'document_id' => $document->id,
                'documentable_type' => Vehicle::class,
                'documentable_id' => $data['vehicle_id'],
            ]);
        } elseif ($data['category'] === 'devices') {
            Documentable::create([
                'document_id' => $document->id,
                'documentable_type' => Device::class,
                'documentable_id' => $data['device_id'],
            ]);
        }

        return response()->json($document, 201);
    }

    public function download(Document $document, Request $request)
    {
        abort_if($document->user_id !== $request->user()->id, 403);

        if (!Storage::disk('local')->exists($document->path)) {
            return response()->json(['message' => 'Plik nie istnieje na dysku.'], 404);
        }

        // zwraca plik z oryginalną nazwą
        return Storage::disk('local')->download($document->path, $document->original_name);
    }

    public function destroy(Document $document, Request $request)
    {
        abort_if($document->user_id !== $request->user()->id, 403);

        // Twarde usuwanie
        Storage::disk('local')->delete($document->path);
        $document->delete();

        return response()->json(['deleted' => true]);
    }
}
