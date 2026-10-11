<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PetPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'photo', 'is_primary'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected $appends = ['photo_url'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        return Storage::disk('public')->url($this->photo);
    }
}
