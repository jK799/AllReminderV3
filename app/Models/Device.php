<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'model',
        'serial_number',
        'purchase_date',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
