<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DesignatedVideo extends Migration
{
    public function up()
    {
        Schema::create('designated_videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('group_id')->nullable()->comment('頻道 ID');
            $table->unsignedInteger('tba_id')->nullable()->comment('影片 ID');
            $table->string('team_model_id')->nullable()->comment('醍摩豆帳號');
            $table->boolean('view')->comment('觀看');
            $table->boolean('comment')->comment('點評');
            $table->boolean('score')->comment('打分數');
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('tba_id')->references('id')->on('tbas');
        });
    }

    public function down()
    {

        Schema::table('designated_videos', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
            $table->dropForeign(['tba_id']);
            $table->dropColumn('tba_id');
        });
        Schema::dropIfExists('designated_videos');

    }
}
