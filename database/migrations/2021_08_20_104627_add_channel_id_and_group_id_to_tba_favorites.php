<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelIdAndGroupIdToTbaFavorites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tba_favorites', function (Blueprint $table) {
            $table->unsignedInteger('channel_id')->nullable()->after('user_id')->comment('channel id');
            $table->unsignedInteger('group_id')->nullable()->after('channel_id')->comment('group id');
            $table->foreign('channel_id')->references('id')->on('group_channels');
            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tba_favorites', function (Blueprint $table) {
            $table->dropForeign(['channel_id']);
            $table->dropForeign(['group_id']);
            $table->dropColumn('channel_id');
            $table->dropColumn('group_id');
        });
    }
}
