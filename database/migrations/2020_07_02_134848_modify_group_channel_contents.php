<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyGroupChannelContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_channel_contents', function (Blueprint $table) {
            $table->unsignedInteger('grades_id')->after('content_public')->nullable();
            $table->unsignedInteger('group_subject_fields_id')->after('content_public')->nullable();
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
        Schema::table('group_channel_contents', function (Blueprint $table) {
            $table->dropForeign(['grades_id']);
            $table->dropForeign(['group_subject_fields_id']);
            $table->dropColumn('grades_id');
            $table->dropColumn('group_subject_fields_id');
        });
    }
}
