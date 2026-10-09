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

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('species_id')
                ->constrained('species')
                ->restrictOnDelete();

            $table->foreignId('breed_id')
                ->nullable()
                ->constrained('breeds')
                ->nullOnDelete();

            $table->string('name');
            $table->string('color')->nullable();

            $table->enum('gender', [
                'male',
                'female',
                'unknown'
            ])->default('unknown');

            $table->unsignedInteger('age')->nullable();

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
