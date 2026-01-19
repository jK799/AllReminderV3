<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Documentable extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'documentable_type',
        'documentable_id',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
