<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusDefaultValueToBbLicenseGroup extends Migration
{
    public function up()
    {
        Schema::table('bb_license_group', function (Blueprint $table) {
            $table->boolean('status')->default(true)->comment('功能開關 true false')->change();
        });
    }

    public function down()
    {
        Schema::table('bb_license_group', function (Blueprint $table) {
            $table->boolean('status')->default(false)->comment('功能開關 true false')->change();
        });
    }
}
