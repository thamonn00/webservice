<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เพิ่ม index ให้คอลัมน์ที่ค้นหาบ่อย (type/status/พิกัด/วันที่/ต่างประเทศคีย์รูป)
     * ของเดิมไม่มี index เลย ค้นหาสัตว์หายจะช้าเมื่อข้อมูลเยอะ
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->index('type');
            $table->index('status');
            $table->index(['latitude', 'longitude']);
            $table->index('seen_at');
            $table->index('pet_id');
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->index('species_id');
            $table->index('breed_id');
        });

        Schema::table('sightings', function (Blueprint $table) {
            $table->index('report_id');
            $table->index('seen_at');
        });

        Schema::table('pet_photos', function (Blueprint $table) {
            $table->index('pet_id');
        });

        Schema::table('report_photos', function (Blueprint $table) {
            $table->index('report_id');
        });

        Schema::table('sighting_photos', function (Blueprint $table) {
            $table->index('sighting_id');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['seen_at']);
            $table->dropIndex(['pet_id']);
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->dropIndex(['species_id']);
            $table->dropIndex(['breed_id']);
        });

        Schema::table('sightings', function (Blueprint $table) {
            $table->dropIndex(['report_id']);
            $table->dropIndex(['seen_at']);
        });

        Schema::table('pet_photos', function (Blueprint $table) {
            $table->dropIndex(['pet_id']);
        });

        Schema::table('report_photos', function (Blueprint $table) {
            $table->dropIndex(['report_id']);
        });

        Schema::table('sighting_photos', function (Blueprint $table) {
            $table->dropIndex(['sighting_id']);
        });
    }
};
