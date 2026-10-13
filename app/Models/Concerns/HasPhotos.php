<?php

namespace App\Models\Concerns;

use App\Models\Photo;

trait HasPhotos
{
    public function photos()
    {
        return $this->morphMany(Photo::class, 'photoable')->orderByDesc('is_primary')->orderBy('id');
    }

    /** ลบรูปทั้งหมด (รวมไฟล์) เมื่อลบเจ้าของรูป */
    protected static function bootHasPhotos()
    {
        static::deleting(function ($model) {
            $model->photos()->get()->each->delete();
        });
    }
}
