<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hire_pos_ticket_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hire_position_id')->constrained('hire_positions')->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->unsignedInteger('granted_quantity')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hire_pos_ticket_requests');
    }
};
