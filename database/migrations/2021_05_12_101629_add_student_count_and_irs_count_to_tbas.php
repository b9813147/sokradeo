<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentCountAndIrsCountToTbas extends Migration
{
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->integer('student_count')->after('playlisted')->default(0)->comment('學生數量');
            $table->integer('irs_count')->after('playlisted')->default(0)->comment('反饋次數');
        });
    }

    public function down()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('student_count');
            $table->dropColumn('irs_count');
        });
    }
}
