<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTbaCommentIdToTbaEvaluateEvents extends Migration
{
    public function up()
    {
        Schema::table('tba_evaluate_events', function (Blueprint $table) {
            $table->unsignedBigInteger('tba_comment_id')->after('time_point')->nullable();
            $table->foreign('tba_comment_id')->references('id')->on('tba_comments')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('tba_evaluate_events', function (Blueprint $table) {
            $table->dropForeign(['tba_comment_id']);
            $table->dropColumn('tba_comment_id');
        });
    }
}
