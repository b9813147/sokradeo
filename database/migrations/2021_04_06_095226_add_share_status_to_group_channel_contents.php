<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShareStatusToGroupChannelContents extends Migration
{
    public function up()
    {
        Schema::table('group_channel_contents', function (Blueprint $table) {
            $table->addColumn('boolean', 'share_status')->default(0)->after('content_public')->comment('分享影片');
        });
    }

    public function down()
    {
        Schema::table('group_channel_contents', function (Blueprint $table) {
            $table->dropColumn('share_status');
        });
    }
}
