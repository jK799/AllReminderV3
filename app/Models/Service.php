<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'vehicle_id',
        'title',
        'description',
        'last_done_at',
        'next_due_at',
        'interval_value',
        'interval_unit',
        'is_active',
    ];

    protected $casts = [
        'last_done_at' => 'date',
        'next_due_at'  => 'date',
        'is_active'    => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
