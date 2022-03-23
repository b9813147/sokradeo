<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveNotificationMessageDataToGroupChannels extends Migration
{
    public function up()
    {
        Schema::table('group_channels', function (Blueprint $table) {
            $table->dropColumn('notification_message_data');
        });
    }

    public function down()
    {
        Schema::table('group_channels', function (Blueprint $table) {
            $table->json('notification_message_data')->after('stage')->default(new Expression('(JSON_OBJECT())'))->comment('通知資訊');

        });
    }
}
