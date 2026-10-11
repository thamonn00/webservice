<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReportPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['report_id', 'photo'];

    protected $appends = ['photo_url'];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        return Storage::disk('public')->url($this->photo);
    }
}
