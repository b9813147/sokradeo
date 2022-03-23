<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictGroupSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_group_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('group_subject_fields_id')->nullable();
            $table->unsignedInteger('district_subjects_id')->nullable();

            $table->timestamps();
            $table->foreign('group_subject_fields_id')->references('id')->on('group_subject_fields');
            $table->foreign('district_subjects_id')->references('id')->on('district_subjects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('district_group_subjects', function (Blueprint $table) {
            $table->dropForeign(['group_subject_fields_id']);
            $table->dropForeign(['district_subjects_id']);
        });
        Schema::dropIfExists('district_group_subjects');
    }
}
