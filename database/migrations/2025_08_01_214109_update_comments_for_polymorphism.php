<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            // Удаляем старые связи
            $table->dropForeign(['game_id']);
            $table->dropColumn('game_id');

            // Добавляем morph-поля
            $table->unsignedBigInteger('commentable_id')->nullable();
            $table->string('commentable_type')->nullable();
        });

        // Обновим существующие записи — если хочешь сразу переложить
        DB::table('comments')
            ->whereNotNull('commentable_id') // безопасно на случай повторного запуска
            ->update([
                'commentable_type' => 'App\\Models\\Game',
                'commentable_id' => DB::raw('id') // Предположим, id игры совпадает
            ]);
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['commentable_id', 'commentable_type']);
            $table->unsignedBigInteger('game_id')->nullable();
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });
    }

};
