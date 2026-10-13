<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // เก็บชื่อสั้น ๆ ใน photoable_type แทนชื่อคลาสเต็ม (เฉพาะ photos)
        // ใช้ morphMap แทน enforceMorphMap เพื่อไม่ให้กระทบ tokenable_type เก่าของ Sanctum
        Relation::morphMap([
            'pet' => \App\Models\Pet::class,
            'report' => \App\Models\Report::class,
        ]);
    }
}
