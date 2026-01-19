<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'last_done_at',
        'next_due_at',
        'interval_value',
        'interval_unit',
        'is_active',
        'device_id',
        'vehicle_id',
    ];

    protected $casts = [
        'last_done_at' => 'date',
        'next_due_at' => 'datetime',
        'interval_value' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
