<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'remind_at',
        'user_id',
        'remindable_type',
        'remindable_id',
    ];

    protected $casts = [
        'remind_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function remindable()
    {
        return $this->morphTo();
    }
}
