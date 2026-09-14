<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mechanics', function (Blueprint $table) {
            $table->dropUnique('mechanics_title_unique');
        });
    }

    public function down(): void
    {
        Schema::table('mechanics', function (Blueprint $table) {
            $table->unique('title');
        });
    }
};
