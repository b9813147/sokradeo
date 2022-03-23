<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject', 100)->nullable()->comment('學科名稱');
            $table->string('alias', 100)->nullable()->comment('學科別名');
            $table->integer('order')->nullable()->comment('排序');
            $table->unsignedInteger('districts_id');
            $table->unsignedInteger('subject_fields_id')->nullable();

            $table->timestamps();
            $table->foreign('districts_id')->references('id')->on('districts');
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
        Schema::table('district_subjects', function (Blueprint $table) {
            $table->dropForeign(['districts_id']);
            $table->dropForeign(['subject_fields_id']);
        });
        Schema::dropIfExists('district_subjects');
    }
}
