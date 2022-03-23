<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStageToGroupChannels extends Migration
{
    public function up()
    {
        Schema::table('group_channels', function (Blueprint $table) {
            $table->integer('stage')->after('public')->default(0)->comment('活動階段');
        });
    }

    public function down()
    {
        Schema::table('group_channels', function (Blueprint $table) {
            $table->dropColumn('stage');
        });
    }
}
