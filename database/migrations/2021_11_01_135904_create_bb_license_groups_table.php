<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBbLicenseGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bb_license_group', function (Blueprint $table) {
            $table->unsignedBigInteger('bb_license_id');
            $table->unsignedInteger('group_id');
            $table->integer('storage')->comment('基本單位MB');
            $table->boolean('status')->default(false)->comment('功能開關 true false');
            $table->date('deadline')->comment('最後期限');
            $table->timestamps();

            $table->foreign('bb_license_id')->references('id')->on('bb_licenses');
            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bb_license_group', function (Blueprint $table) {
            $table->dropForeign(['bb_license_id']);
            $table->dropForeign(['group_id']);
        });
        Schema::dropIfExists('bb_license_group');
    }
}
