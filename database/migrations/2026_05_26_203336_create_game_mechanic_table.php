<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_mechanic', function (Blueprint $table) {
            $table->id();

            // Foreign key referencing your games table
            $table->unsignedBigInteger('game_id');
            $table->foreign('game_id')->references('game_id')->on('games')->onDelete('cascade');

            // Foreign key referencing your mechanics table
            $table->unsignedBigInteger('mechanic_id');
            $table->foreign('mechanic_id')->references('mechanic_id')->on('mechanics')->onDelete('cascade');

            $table->timestamps();

            // Prevent duplicate combination rows
            $table->unique(['game_id', 'mechanic_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_mechanic');
    }
};
