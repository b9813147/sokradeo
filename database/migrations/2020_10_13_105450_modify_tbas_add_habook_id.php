<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTbasAddHabookId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->string('habook_id')->nullable()->after('teacher')->comment('授課老師');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('habook_id');
        });
    }
}
