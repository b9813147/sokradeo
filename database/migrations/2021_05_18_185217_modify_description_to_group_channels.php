<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDescriptionToGroupChannels extends Migration
{
    public function up()
    {
        Schema::table('group_channels', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }

    public function down()
    {
        Schema::table('group_channels', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }
}
