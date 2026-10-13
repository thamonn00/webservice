<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // สัตว์เลี้ยงของสมาชิก
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('species_id')->constrained('species')->restrictOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained('breeds')->nullOnDelete();
            $table->string('name', 100);
            $table->string('color', 100)->nullable();
            $table->enum('gender', ['male', 'female', 'unknown'])->default('unknown');
            $table->unsignedSmallInteger('age')->nullable();      // อายุ (ปี)
            $table->text('description')->nullable();               // ลักษณะเด่น ปลอกคอ ไมโครชิป
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pets');
    }
};
