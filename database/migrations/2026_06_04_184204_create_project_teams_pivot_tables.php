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
        // 💡 Add these three lines at the very top of the up() method to clear the roadblock
        Schema::dropIfExists('project_participants');
        Schema::dropIfExists('project_editors');
        Schema::dropIfExists('project_owners');

        // 1. Project Owners Pivot Table
        Schema::create('project_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });

        // 2. Project Editors Pivot Table
        Schema::create('project_editors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });

        // 3. Project Participants Pivot Table
        Schema::create('project_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function downgrade(): void
    {
        Schema::dropIfExists('project_participants');
        Schema::dropIfExists('project_editors');
        Schema::dropIfExists('project_owners');
    }
};
