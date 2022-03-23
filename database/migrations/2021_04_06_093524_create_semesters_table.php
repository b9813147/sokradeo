<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemestersTable extends Migration
{
    public function up()
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('semester_id')->default(1)->comment('學期');
            $table->unsignedInteger('group_id');
            $table->integer('year')->index()->nullable()->comment('年');
            $table->integer('month')->index()->nullable()->comment('月');
            $table->integer('day')->index()->nullable()->comment('日');
            $table->string('guid', 32)->nullable()->comment('對接應用');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('semesters', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
        });
        Schema::dropIfExists('semesters');
    }
}
