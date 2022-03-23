<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScoresTable extends Migration
{
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->comment('使用者ID');
            $table->unsignedInteger('group_id')->comment('頻道ID');
            $table->unsignedInteger('tba_id')->comment('影片ID');
            $table->string('comment', 200)->nullable()->comment('評語');
            $table->json('score_data')->default(new Expression('(JSON_OBJECT())'))->comment('成績相關');
            $table->boolean('flag')->default(false)->comment('有效評分');
            $table->boolean('type')->default(false)->comment('一般成績 false 總成績 true');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('tba_id')->references('id')->on('tbas');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['group_id']);
            $table->dropForeign(['tba_id']);
        });
        Schema::dropIfExists('scores');
    }
}
