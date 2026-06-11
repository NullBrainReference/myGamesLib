<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_mechanic', function (Blueprint $table) {
            $table->id();

            // Foreign Key pointing to your Project model (adjust column type if project uses uuid/string)
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');

            // Foreign Key pointing to your Mechanic model
            $table->foreignId('mechanic_id')->constrained('mechanics', 'mechanic_id')->onDelete('cascade');

            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['project_id', 'mechanic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_mechanic');
    }
};
