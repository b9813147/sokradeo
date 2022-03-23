<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseCoreAndObservationFocusToTbas extends Migration
{
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->string('course_core', 150)->after('description')->nullable()->comment('課程脈絡');
            $table->string('observation_focus', 150)->after('description')->nullable()->comment('觀課焦點');
        });
    }

    public function down()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('course_core');
            $table->dropColumn('observation_focus');
        });
    }
}
