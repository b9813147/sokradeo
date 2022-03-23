<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observation_classes', function (Blueprint $table) {
            // Define fields
            $table->id();
            $table->integer('user_id')->unsigned()->comment('creator user id');
            $table->string('binding_number')->nullable()->comment('binding number');
            $table->string('classroom_code', 6)->nullable()->comment('classroom code, starts with [ORG: 0, CN: 9]');
            $table->string('pin_code', 4)->nullable()->comment('pin code');
            $table->integer('duration')->unsigned()->comment('class duration');
            $table->string('status', 1)->comment('class status [R: ready(預備中)、S: in-progress(進行中)、E: ending(結束)]');
            $table->integer('timestamp')->unsigned()->nullable()->default(null)->comment('the start time of the observation class');
            $table->integer('group_id')->unsigned()->comment('group id');
            $table->integer('channel_id')->unsigned()->comment('Channel ID');
            $table->string('name')->nullable()->comment('class name');
            $table->integer('content_public')->unsigned()->comment('content public status [1: private, 2: public, 3: global]');
            $table->string('teacher', 32)->nullable()->comment('teacher name');
            $table->string('habook_id')->nullable()->comment('teacher habook id');
            $table->integer('rating_id')->unsigned()->comment('group category id');
            $table->integer('group_subject_field_id')->unsigned()->comment('group subject id');
            $table->integer('grade_id')->unsigned()->comment('grades id [1-12]');
            $table->dateTime('lecture_date')->comment('lecture date');
            $table->integer('locale_id')->unsigned()->comment('locale id');
            $table->timestamps();

            // Define relationships
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('channel_id')->references('id')->on('group_channels');
            $table->foreign('rating_id')->references('id')->on('ratings');
            $table->foreign('group_subject_field_id')->references('id')->on('group_subject_fields');
            $table->foreign('grade_id')->references('id')->on('grades');
            $table->foreign('locale_id')->references('id')->on('locales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observation_classes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['group_id']);
            $table->dropForeign(['rating_id']);
            $table->dropForeign(['group_subject_field_id']);
            $table->dropForeign(['grade_id']);
            $table->dropForeign(['locale_id']);
        });
        Schema::dropIfExists('observation_classes');
    }
}
