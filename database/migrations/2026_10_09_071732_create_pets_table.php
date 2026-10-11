<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            //เก็บเจ้าของสัตว์เลี้ยง
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            //เก็บชนิดสัตว์ (หมา แมว ฯลฯ)
            $table->foreignId('species_id')
                ->constrained('species')
                ->restrictOnDelete();
            //เก็บสายพันธุ์
            $table->foreignId('breed_id')
                ->nullable()
                ->constrained('breeds')
                ->nullOnDelete();
            //เก็บชื่อสัตว์เลี้ยง
            $table->string('name');
            $table->string('color')->nullable();
            //เก็บเพศสัตว์เลี้ยง
            $table->enum('gender', [
                'male',
                'female',
                'unknown'
            ])->default('unknown');
            //เก็บอายุสัตว์เลี้ยง
            $table->unsignedInteger('age')->nullable();
            //คำอธิบายเพิ่มเติม เป็นข้อความยาวได้ เช่น ลักษณะเด่น ปลอกคอ ปุ่มไฝ
            $table->text('description')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pets');
    }
};
