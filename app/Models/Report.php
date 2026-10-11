<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'user_id',
        'type',
        'title',
        'description',
        'province',
        'district',
        'subdistrict',
        'place',
        'latitude',
        'longitude',
        'seen_at',
        'contact_phone',
        'status',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function photos()
    {
        return $this->hasMany(ReportPhoto::class);
    }

    public function sightings()
    {
        return $this->hasMany(Sighting::class);
    }

    /**
     * กรอบสี่เหลี่ยมคร่าว ๆ รอบพิกัด (ใช้กรองก่อนคำนวณระยะจริงด้วย Haversine)
     * 1 องศาละติจูด ≈ 111.32 กม.
     */
    public function scopeWithinBox($query, float $lat, float $lng, float $radiusKm)
    {
        $latDelta = $radiusKm / 111.32;
        $lngDelta = $radiusKm / max(0.2, 111.32 * cos(deg2rad($lat)));

        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);
    }

    /**
     * ระยะทาง Haversine หน่วยกิโลเมตร
     */
    public static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(min(1, sqrt($a)));
    }
}
