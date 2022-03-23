<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTbaCommentIdToTbaCommentAttaches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tba_comment_attaches', function (Blueprint $table) {
            $table->dropForeign(['tba_comment_id']);
            $table->foreign('tba_comment_id')->references('id')->on('tba_comments')->cascadeOnDelete();
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
            $table->foreign('tba_comment_id')->references('id')->on('tba_comments');
        });
    }
}
