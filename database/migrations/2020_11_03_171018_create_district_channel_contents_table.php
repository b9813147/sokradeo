<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictChannelContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_channel_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('content_id');
            $table->unsignedInteger('districts_id');
            $table->unsignedInteger('groups_id');
            $table->unsignedInteger('ratings_id');
            $table->unsignedInteger('grades_id')->nullable();
            $table->unsignedInteger('group_subject_fields_id')->nullable();
            $table->timestamps();

            $table->foreign('districts_id')->references('id')->on('districts');
            $table->foreign('groups_id')->references('id')->on('groups');
            $table->foreign('ratings_id')->references('id')->on('ratings');
            $table->foreign('grades_id')->references('id')->on('grades');
            $table->foreign('group_subject_fields_id')->references('id')->on('group_subject_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('district_channel_contents', function (Blueprint $table) {
            $table->dropForeign(['districts_id']);
            $table->dropForeign(['groups_id']);
            $table->dropForeign(['ratings_id']);
            $table->dropForeign(['grades_id']);
            $table->dropForeign(['group_subject_fields_id']);
        });
        Schema::dropIfExists('district_channel_contents');
    }
}
