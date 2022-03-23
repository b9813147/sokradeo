<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecommendedVideos extends Migration
{
    public function up()
    {
        Schema::create('recommended_videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('tba_id')->comment('影片 ID');
            $table->unsignedInteger('group_channel_id')->comment('影片來源頻道');
            $table->unsignedInteger('district_id')->nullable()->comment('由學區內推薦時使用');
            $table->unsignedInteger('group_id')->nullable()->comment('由頻道內推薦時使用');
            $table->integer('order')->nullable()->comment('排序');
            $table->timestamps();

            $table->foreign('tba_id')->references('id')->on('tbas');
            $table->foreign('group_channel_id')->references('id')->on('group_channels');
            $table->foreign('district_id')->references('id')->on('districts');
            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    public function down()
    {
        Schema::table('recommended_videos', function (Blueprint $table) {
            $table->dropForeign(['tba_id']);
            $table->dropColumn('tba_id');
            $table->dropForeign(['group_channel_id']);
            $table->dropColumn('group_channel_id');
            $table->dropForeign(['district_id']);
            $table->dropColumn('district_id');
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
        Schema::dropIfExists('recommended_videos');
    }
}
