<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->decimal('average_rating', 3, 1)->default(0)->after('img_src');

            $table->unsignedInteger('ratings_count')->default(0)->after('average_rating');

            $table->index('average_rating');
            $table->index('ratings_count');
        });
    }

    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropIndex(['average_rating']);
            $table->dropIndex(['ratings_count']);
            $table->dropColumn(['average_rating', 'ratings_count']);
        });
    }
};
