<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->string('type', 10)->comment('編輯狀態 C  U  D');
            $table->integer('user_id')->nullable()->comment('修改者');
            $table->integer('tba_id')->nullable();
            $table->integer('district_user_id')->nullable()->comment('學區使用者');
            $table->integer('group_subject_field_id')->nullable()->comment('頻道學科');
            $table->integer('district_group_subject_id')->nullable()->comment('學科歸類(classification)');
            $table->integer('district_subject_id')->nullable()->comment('學區學科');
            $table->integer('district_channel_content_id')->nullable()->comment('學區課例');
            $table->integer('rating_id')->nullable()->comment('分類');
            $table->integer('group_id')->nullable()->comment('頻道');
            $table->integer('district_id')->nullable()->comment('學區');
            $table->integer('user')->nullable()->comment('使用者');
            $table->timestamps();

//            $table->foreign('user_id')->references('id')->on('users');
//            $table->foreign('tba_id')->references('id')->on('tbas');
//            $table->foreign('district_user_id')->references('id')->on('district_users');
//            $table->foreign('group_subject_field_id')->references('id')->on('group_subject_fields');
//            $table->foreign('district_group_subject_id')->references('id')->on('district_group_subjects');
//            $table->foreign('district_subject_id')->references('id')->on('district_subjects');
//            $table->foreign('district_channel_content_id')->references('id')->on('district_channel_contents');
//            $table->foreign('rating_id')->references('id')->on('ratings');
//            $table->foreign('group_id')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('records', function (Blueprint $table) {
//            $table->dropForeign(['user_id']);
//            $table->dropForeign(['tba_id']);
//            $table->dropForeign(['district_user_id']);
//            $table->dropForeign(['group_subject_field_id']);
//            $table->dropForeign(['district_group_subject_id']);
//            $table->dropForeign(['district_subject_id']);
//            $table->dropForeign(['district_channel_content_id']);
//            $table->dropForeign(['rating_id']);
//            $table->dropForeign(['group_id']);
//        });
        Schema::dropIfExists('records');
    }
}
