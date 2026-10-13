<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['photoable_type', 'photoable_id', 'path', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    protected $hidden = ['photoable_type', 'photoable_id'];

    protected $appends = ['url'];

    public function photoable()
    {
        return $this->morphTo();
    }

    /** ลิงก์รูปที่ใช้แสดงผล (URL ภายนอกใช้ตรง ๆ ไฟล์อัปโหลดต่อด้วย /storage) */
    public function getUrlAttribute(): string
    {
        if (filter_var($this->path, FILTER_VALIDATE_URL)) {
            return $this->path;                       // URL ภายนอก
        }
        if (str_starts_with($this->path, '/')) {
            return asset(ltrim($this->path, '/'));    // รูปตัวอย่างใน public/
        }

        return asset('storage/'.$this->path);         // ไฟล์ที่อัปโหลด
    }

    /** true = ไม่ใช่ไฟล์ใน storage (URL ภายนอก หรือรูปตัวอย่างใน public) จึงไม่ต้องลบไฟล์ */
    public function isExternal(): bool
    {
        return filter_var($this->path, FILTER_VALIDATE_URL) || str_starts_with($this->path, '/');
    }

    /** ลบไฟล์จริงออกจากดิสก์ด้วยเมื่อลบแถว (ถ้าไฟล์หายไปแล้วให้ข้าม ไม่ throw) */
    protected static function booted()
    {
        static::deleting(function (Photo $photo) {
            if (! $photo->isExternal() && Storage::disk('public')->exists($photo->path)) {
                Storage::disk('public')->delete($photo->path);
            }
        });
    }
}
