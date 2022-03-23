<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupSubjectFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_subject_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject', 100)->nullable()->comment('學科名稱');
            $table->unsignedInteger('groups_id')->nullable();
            $table->unsignedInteger('subject_fields_id')->nullable();
            $table->foreign('groups_id')->references('id')->on('groups');
            $table->foreign('subject_fields_id')->references('id')->on('subject_fields');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_subject_fields', function (Blueprint $table) {
            $table->dropForeign(['groups_id']);
            $table->dropForeign(['subject_fields_id']);
        });
        Schema::dropIfExists('group_subject_fields');
    }
}
