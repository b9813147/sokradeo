<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUserIdToObservationUsers extends Migration
{
    public function up()
    {
        Schema::table('observation_users', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('observation_users', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->change();
        });
    }
}
