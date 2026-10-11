<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sighting extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'message',
        'province',
        'district',
        'subdistrict',
        'seen_place',
        'latitude',
        'longitude',
        'seen_at',
        'contact',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(SightingPhoto::class);
    }
}
