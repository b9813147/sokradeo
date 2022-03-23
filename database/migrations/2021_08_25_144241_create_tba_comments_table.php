<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbaCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_comments', function (Blueprint $table) {
            $table->id();
            $table->string('nick_name')->nullable()->comment('記錄訪客名字');
            $table->string('tag_id')->comment('標籤ID');
            $table->unsignedInteger('tba_id');
            $table->unsignedInteger('user_id');
            $table->tinyInteger('comment_type')->comment('評論類型');
            $table->float('time_point')->comment('打點時間');
            $table->text('text')->comment('內容');

            $table->foreign('tba_id')->references('id')->on('tbas');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tag_id')->references('id')->on('tags');
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
        Schema::table('tba_comments', function (Blueprint $table) {
            $table->dropForeign(['tba_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['tag_id']);
        });
        Schema::dropIfExists('tba_comments');
    }
}
