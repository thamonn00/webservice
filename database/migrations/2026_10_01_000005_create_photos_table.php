<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ตารางรูปภาพตารางเดียว ใช้ร่วมกันทั้งสัตว์เลี้ยง (pet) และประกาศ (report)
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->morphs('photoable');                           // photoable_type + photoable_id (+ index)
            $table->string('path', 2048);                          // path ใน storage หรือ URL ภายนอก
            $table->boolean('is_primary')->default(false);         // รูปหลัก
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('photos');
    }
};
