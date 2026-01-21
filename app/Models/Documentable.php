<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentable extends Model
{
    protected $fillable = [
        'document_id',
        'documentable_type',
        'documentable_id',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
