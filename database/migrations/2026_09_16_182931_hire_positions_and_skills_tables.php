<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Global Skills / Tags
        Schema::create('position_skills', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->timestamps();
        });

        // 2. Open Positions for Projects
        Schema::create('hire_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->unsignedInteger('tickets_amount')->default(1); // Desired headcount / slots
            $table->boolean('is_open')->default(true);
            $table->timestamps();
        });

        // 3. Pivot: Position <-> Skills
        Schema::create('hire_position_skill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hire_position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_skill_id')->constrained()->cascadeOnDelete();
        });

        // 4. Application Tickets submitted by Users
        Schema::create('position_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('hire_positions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('reason'); // Application statement / pitch
            $table->boolean('expired')->default(false);
            $table->boolean('success')->nullable(); // null = pending, true = accepted, false = rejected
            $table->timestamps();

            // Prevent duplicate active applications per user per position
            $table->unique(['position_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_tickets');
        Schema::dropIfExists('hire_position_skill');
        Schema::dropIfExists('hire_positions');
        Schema::dropIfExists('position_skills');
    }
};
