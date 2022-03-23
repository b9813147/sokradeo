<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictTbaInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_tba_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('districts_id');
            $table->unsignedInteger('tbas_id');
            $table->unsignedInteger('subject_fields_id')->nullable();
            $table->string('grade')->nullable();
            $table->timestamps();
            $table->foreign('districts_id')->references('id')->on('districts');
            $table->foreign('tbas_id')->references('id')->on('tbas');
            $table->foreign('subject_fields_id')->references('id')->on('subject_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('district_tba_infos', function (Blueprint $table) {
            $table->dropForeign(['districts_id']);
            $table->dropForeign(['tbas_id']);
            $table->dropForeign(['subject_fields_id']);
        });
        Schema::dropIfExists('district_tba_infos');
    }
}
