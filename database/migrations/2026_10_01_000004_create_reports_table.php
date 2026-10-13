<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ประกาศสัตว์หาย (lost) / ประกาศพบสัตว์ (found)
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // สัตว์ของเรา (ใช้กับประกาศหาย) — ประกาศ "พบ" อาจไม่รู้ว่าเป็นสัตว์ของใคร จึงเป็น null ได้
            $table->foreignId('pet_id')->nullable()->constrained('pets')->nullOnDelete();
            $table->foreignId('species_id')->constrained('species')->restrictOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained('breeds')->nullOnDelete();
            $table->enum('type', ['lost', 'found']);
            $table->string('title', 150);
            $table->string('color', 100)->nullable();
            $table->text('description')->nullable();
            // ที่อยู่เก็บแค่ตำบล (อำเภอ/จังหวัดดึงต่อได้จากความสัมพันธ์)
            $table->foreignId('subdistrict_id')->nullable()->constrained('subdistricts')->nullOnDelete();
            $table->string('place', 255)->nullable();             // จุดสังเกต เช่น หน้าตลาด
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('event_at');                          // วัน-เวลาที่หาย/พบ
            $table->string('contact_phone', 20);
            $table->enum('status', ['open', 'closed'])->default('open'); // closed = ได้คืน/ปิดประกาศแล้ว
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('event_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
