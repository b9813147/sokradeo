<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradeFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('locales_id');
            $table->string('name');
            $table->timestamps();
            $table->foreign('locales_id')->references('id')->on('locales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grade_fields', function (Blueprint $table) {
            $table->dropForeign(['locales_id']);
        });
        Schema::dropIfExists('grade_fields');
    }
}
