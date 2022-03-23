<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradeLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade_langs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('grades_id');
            $table->unsignedInteger('locales_id');
            $table->text('name')->nullable()->comment('年級');
            $table->timestamps();
            $table->foreign('locales_id')->references('id')->on('locales');
            $table->foreign('grades_id')->references('id')->on('grades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grade_langs', function (Blueprint $table) {
            $table->dropForeign(['grades_id']);
            $table->dropForeign(['locales_id']);
        });
        Schema::dropIfExists('grade_langs');
    }
}
