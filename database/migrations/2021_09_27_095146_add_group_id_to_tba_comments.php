<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdToTbaComments extends Migration
{
    public function up()
    {
        Schema::table('tba_comments', function (Blueprint $table) {
            $table->unsignedInteger('group_id')->after('user_id')->nullable()->comment('頻道ID');
            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    public function down()
    {
        Schema::table('tba_comments', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
        });
        Schema::table('tba_comments', function (Blueprint $table) {
            $table->dropColumn('group_id');
        });
    }
}
