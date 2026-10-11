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
        Schema::create('subdistricts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')
                ->constrained('districts')
                ->cascadeOnDelete();

            $table->unsignedInteger('subdistrict_code')->unique();
            $table->string('name_th', 100); // ชื่อตำบล
            $table->string('name_en', 100)->nullable();
            $table->string('postal_code', 10)->nullable(); // รหัสไปรษณีย์
            $table->timestamps();

            $table->unique(['district_id', 'name_th']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subdistricts');
    }
};
