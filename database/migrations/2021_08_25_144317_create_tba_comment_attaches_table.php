<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbaCommentAttachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_comment_attaches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tba_comment_id');
            $table->string('name', 32)->nullable()->comment('圖片名稱');
            $table->string('ext', 16)->nullable()->comment('副檔名');
            $table->text('image_url')->nullable()->comment('圖片連結');
            $table->text('preview_url')->nullable()->comment('圖片連結');

            $table->foreign('tba_comment_id')->references('id')->on('tba_comments');
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
        Schema::table('tba_comment_attaches', function (Blueprint $table) {
            $table->dropForeign(['tba_comment_id']);
        });
        Schema::dropIfExists('tba_comment_attaches');
    }
}
