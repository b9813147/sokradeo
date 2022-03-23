<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupIdAndTypeToNotificationMessages extends Migration
{
    public function up()
    {
        Schema::table('notification_messages', function (Blueprint $table) {
            $table->tinyInteger('type')->after('status')->default(1)->comment('通知類型');
            $table->unsignedInteger('group_id')->after('user_id')->nullable()->comment('頻道ID');

            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    public function down()
    {
        Schema::table('notification_messages', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
            $table->dropColumn('type');
        });
    }
}
