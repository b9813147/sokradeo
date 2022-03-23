<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDoubleGreenLightToTbas extends Migration
{
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->addColumn('boolean', 'double_green_light_status')->default(0)->comment('雙綠燈');
        });
    }

    public function down()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('double_green_light_status');
        });
    }
}
