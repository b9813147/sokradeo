<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelIdToExhibitionCmsSets extends Migration
{
    public function up()
    {
        Schema::table('exhibition_cms_sets', function (Blueprint $table) {
            $table->addColumn('integer', 'channel_id')->after('cms_id')->nullable()->comment('頻道ID');
        });
    }

    public function down()
    {
        Schema::table('exhibition_cms_sets', function (Blueprint $table) {
            $table->dropColumn('channel_id');
        });
    }
}
