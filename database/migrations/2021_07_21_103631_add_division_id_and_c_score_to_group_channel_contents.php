<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisionIdAndCScoreToGroupChannelContents extends Migration
{
    public function up()
    {
        Schema::table('group_channel_contents', function (Blueprint $table) {
            $table->unsignedBigInteger('division_id')->nullable()->after('ratings_id')->comment('分組ID');
            $table->json('c_score')->default(new Expression('(JSON_OBJECT())'))->after('ratings_id')->comment('成績相關');

            $table->foreign('division_id')->references('id')->on('divisions');
        });
    }

    public function down()
    {
        Schema::table('group_channel_contents', function (Blueprint $table) {
            $table->dropColumn('c_score');
            $table->dropForeign(['division_id']);
            $table->dropColumn('division_id');
        });
    }
}
