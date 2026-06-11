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
        Schema::table('mechanics', function (Blueprint $table) {
            // 💡 Default to true or false based on your preferred design pipeline setup
            $table->boolean('approved')->default(false)->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mechanics', function (Blueprint $table) {
            $table->dropColumn('approved');
        });
    }
};
