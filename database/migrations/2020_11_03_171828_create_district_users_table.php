<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('districts_id');
            $table->unsignedInteger('user_id');
            $table->integer('member_status');
            $table->string('member_duty');
            $table->timestamps();
            $table->foreign('districts_id')->references('id')->on('districts');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('district_users', function (Blueprint $table) {
            $table->dropForeign(['districts_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('district_users');
    }
}
