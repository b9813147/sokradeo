<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDescriptionToGroupLangs extends Migration
{
    public function up()
    {
        Schema::table('group_langs', function (Blueprint $table) {
            $table->text('description')->change();
        });
    }

    public function down()
    {
        Schema::table('group_langs', function (Blueprint $table) {
            $table->string('description')->change();
        });
    }
}
