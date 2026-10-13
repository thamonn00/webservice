<?php

namespace App\Models;

use App\Models\Concerns\HasPhotos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory, HasPhotos;

    protected $fillable = ['user_id', 'species_id', 'breed_id', 'name', 'color', 'gender', 'age', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function getGenderLabelAttribute()
    {
        return ['male' => 'ผู้', 'female' => 'เมีย', 'unknown' => 'ไม่ระบุ'][$this->gender] ?? '-';
    }
}
