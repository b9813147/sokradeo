<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTypeToScores extends Migration
{
    public function up()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    public function down()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->boolean('type')->after('flag')->default(0)->comment('一般成績 false 總成績 true');
        });
    }
}
