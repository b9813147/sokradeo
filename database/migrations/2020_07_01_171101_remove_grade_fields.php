<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveGradeFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('grade_fields');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('grade_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('locales_id');
            $table->string('name');
            $table->timestamps();
            $table->foreign('locales_id')->references('id')->on('locales');
        });
    }
}
