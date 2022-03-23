<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuestToObservationUsers extends Migration
{
    public function up()
    {
        Schema::table('observation_users', function (Blueprint $table) {
            $table->string('guest')->after('user_id')->comment('訪客ID');
        });
    }

    public function down()
    {
        Schema::table('observation_users', function (Blueprint $table) {
            $table->dropColumn('guest');
        });
    }
}
