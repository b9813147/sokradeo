<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyObservationOffsetToTbas extends Migration
{
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('observation_offset');
        });
        Schema::table('tbas', function (Blueprint $table) {
            $table->double('observation_offset', 8, 4)->default(0.0000)->after('student_count');
        });
    }

    public function down()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->float('observation_offset')->change()->default(0)->after('student_count');
        });
    }
}
