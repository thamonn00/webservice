<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breed extends Model
{
    use HasFactory;

    protected $fillable = ['species_id', 'name'];

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
