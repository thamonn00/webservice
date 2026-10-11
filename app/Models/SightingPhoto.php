<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SightingPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['sighting_id', 'photo'];

    protected $appends = ['photo_url'];

    public function sighting()
    {
        return $this->belongsTo(Sighting::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        return Storage::disk('public')->url($this->photo);
    }
}
