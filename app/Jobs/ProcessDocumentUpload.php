<?php

namespace App\Jobs;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDocumentUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $documentId;

    public function __construct(int $documentId)
    {
        $this->documentId = $documentId;
    }

    public function handle(): void
    {
        $document = Document::find($this->documentId);

        if (!$document) {
            return;
        }

        // Symulacja przetwarzania dokumentu w tle
        Log::info('Document processed asynchronously', [
            'document_id' => $document->id,
            'user_id' => $document->user_id,
            'file' => $document->original_name,
        ]);
    }
}