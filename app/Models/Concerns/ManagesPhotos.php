<?php

namespace App\Models\Concerns;

use Illuminate\Http\Request;
use RuntimeException;

/** ใช้ใน controller: เพิ่ม/ลบรูปของ Pet หรือ Report ผ่าน photos[] (ไฟล์) / photo_urls[] / delete_photo_ids[] */
trait ManagesPhotos
{
    protected function photoRules(): array
    {
        return [
            'photos' => ['sometimes', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photo_urls' => ['sometimes', 'array', 'max:10'],
            'photo_urls.*' => ['url', 'max:2048'],
            'delete_photo_ids' => ['sometimes', 'array'],
            'delete_photo_ids.*' => ['integer'],
        ];
    }

    protected function syncPhotos($model, Request $request, string $folder): void
    {
        // ลบรูปที่ระบุ (เฉพาะรูปของ model นี้) — ใช้ each->delete เพื่อให้ลบไฟล์จริงด้วย
        if ($ids = $request->input('delete_photo_ids')) {
            $model->photos()->whereIn('id', $ids)->get()->each->delete();
        }

        $hasPrimary = $model->photos()->where('is_primary', true)->exists();
        $add = function (string $path) use ($model, &$hasPrimary) {
            $model->photos()->create(['path' => $path, 'is_primary' => ! $hasPrimary]);
            $hasPrimary = true;
        };

        foreach (($request->file('photos') ?? []) as $file) {
            $path = $file->store($folder, 'public');
            if ($path === false) {
                throw new RuntimeException('Unable to store uploaded photo.');
            }

            $add($path);
        }
        foreach (($request->input('photo_urls') ?? []) as $url) {
            $add($url);
        }

        // ถ้ารูปหลักถูกลบ ให้รูปแรกที่เหลือเป็นรูปหลัก
        if (! $hasPrimary && ($first = $model->photos()->first())) {
            $first->update(['is_primary' => true]);
        }
    }
}
