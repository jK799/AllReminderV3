<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Documentable;
use App\Models\Vehicle;
use App\Models\Device;
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
            'category' => ['required', Rule::in(['vehicles', 'devices', 'general'])],
            'vehicle_id' => ['nullable', 'integer'],
            'device_id' => ['nullable', 'integer'],
        ]);

        $file = $data['file'];
        $user = $request->user();

        $path = $file->store(
            'documents/' . $user->id,
            'local'
        );

        $document = Document::create([
            'user_id' => $user->id,
            'title' => $data['title'] ?? null,
            'notes' => $data['notes'] ?? null,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        // POWIĄZANIA
        if ($data['category'] === 'vehicles' && $data['vehicle_id']) {
            Documentable::create([
                'document_id' => $document->id,
                'documentable_type' => Vehicle::class,
                'documentable_id' => $data['vehicle_id'],
            ]);
        }

        if ($data['category'] === 'devices' && $data['device_id']) {
            Documentable::create([
                'document_id' => $document->id,
                'documentable_type' => Device::class,
                'documentable_id' => $data['device_id'],
            ]);
        }

        return response()->json($document, 201);
    }

    public function destroy(Document $document, Request $request)
    {
        abort_if($document->user_id !== $request->user()->id, 403);

        Storage::delete($document->path);
        $document->delete();

        return response()->json(['deleted' => true]);
    }
}
