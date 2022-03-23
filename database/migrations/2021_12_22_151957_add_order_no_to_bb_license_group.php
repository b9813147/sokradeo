<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderNoToBbLicenseGroup extends Migration
{
    public function up()
    {
        Schema::table('bb_license_group', function (Blueprint $table) {
            $table->string('order_no')->index()->unique()->nullable()->after('bb_license_id')->comment('訂單編號');
        });
    }

    public function down()
    {
        Schema::table('bb_license_group', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });
    }
}
