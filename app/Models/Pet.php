<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'species_id',
        'breed_id',
        'name',
        'color',
        'gender',
        'age',
        'description',
    ];

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

    public function photos()
    {
        return $this->hasMany(PetPhoto::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
