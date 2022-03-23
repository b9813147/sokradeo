<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivisionUserTable extends Migration
{
    public function up()
    {
        Schema::create('division_user', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->comment('使用者ID');
            $table->unsignedBigInteger('division_id')->comment('分組ID');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('division_id')->references('id')->on('divisions');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('division_user', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['division_id']);
        });

        Schema::dropIfExists('division_user');
    }
}
