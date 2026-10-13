<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ชนิดสัตว์ เช่น สุนัข แมว
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        // สายพันธุ์ (ผูกกับชนิดสัตว์)
        Schema::create('breeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->string('name', 100);
            $table->timestamps();

            $table->unique(['species_id', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('breeds');
        Schema::dropIfExists('species');
    }
};
