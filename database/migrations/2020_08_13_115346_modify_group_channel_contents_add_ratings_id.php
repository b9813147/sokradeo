<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyGroupChannelContentsAddRatingsId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_channel_contents', function (Blueprint $table) {
            $table->unsignedInteger('ratings_id')->after('grades_id')->nullable();
            $table->foreign('ratings_id')->references('id')->on('ratings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_channel_contents', function (Blueprint $table) {
            $table->dropForeign(['ratings_id']);
            $table->dropColumn('ratings_id');
        });
    }
}
