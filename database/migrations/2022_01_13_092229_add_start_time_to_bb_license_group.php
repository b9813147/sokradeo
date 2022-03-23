<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartTimeToBbLicenseGroup extends Migration
{
    public function up()
    {
        Schema::table('bb_license_group', function (Blueprint $table) {
            $table->date('start_time')->after('status')->comment('開始時間');
        });
    }

    public function down()
    {
        Schema::table('bb_license_group', function (Blueprint $table) {
            $table->dropColumn('start_time');
        });
    }
}
