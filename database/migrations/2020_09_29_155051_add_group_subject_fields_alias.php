<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupSubjectFieldsAlias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_subject_fields', function (Blueprint $table) {
            $table->string('alias',100)->after('subject')->nullable()->comment('別名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_subject_fields', function (Blueprint $table) {
            $table->dropColumn('alias');
        });
    }
}
