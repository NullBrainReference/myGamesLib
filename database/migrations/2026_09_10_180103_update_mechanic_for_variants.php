<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mechanics', function (Blueprint $table) {
            // Games uses 'game_id' primary key
            if (!Schema::hasColumn('mechanics', 'game_id')) {
                $table->foreignId('game_id')
                    ->nullable()
                    ->constrained('games', 'game_id')
                    ->nullOnDelete();
            }

            // Projects uses default 'id' primary key
            if (!Schema::hasColumn('mechanics', 'project_id')) {
                $table->foreignId('project_id')
                    ->nullable()
                    ->constrained('projects')
                    ->nullOnDelete();
            }

            // Variants use custom 'mechanic_id' primary key
            if (!Schema::hasColumn('mechanics', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->foreign('parent_id')
                    ->references('mechanic_id')
                    ->on('mechanics')
                    ->cascadeOnDelete();
            }

            $table->index(['game_id', 'project_id', 'approved']);
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('mechanics', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['parent_id']);

            $table->dropIndex(['game_id', 'project_id', 'approved']);
            $table->dropIndex(['parent_id']);

            $table->dropColumn(['game_id', 'project_id', 'parent_id']);
        });
    }
};
