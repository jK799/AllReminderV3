<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

