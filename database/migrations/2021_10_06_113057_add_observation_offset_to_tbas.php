<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObservationOffsetToTbas extends Migration
{
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->float('observation_offset')->default(0)->after('student_count');
        });
    }

    public function down()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('observation_offset');
        });
    }
}
