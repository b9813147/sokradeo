<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictSubjectFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_subject_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject', 100)->nullable()->comment('學科名稱');
            $table->unsignedInteger('districts_id');
            $table->unsignedInteger('subject_fields_id')->nullable();;
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
        Schema::table('district_subject_fields', function (Blueprint $table) {
            $table->dropForeign(['districts_id']);
            $table->dropForeign(['subject_fields_id']);
        });
        Schema::dropIfExists('district_subject_fields');
    }
}
