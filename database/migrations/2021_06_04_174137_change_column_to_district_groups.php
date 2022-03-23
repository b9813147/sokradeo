<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnToDistrictGroups extends Migration
{
    public function up()
    {
        Schema::table('district_groups', function (Blueprint $table) {
            $table->integer('list_top')->default(0)->change()->comment('置頂');
            $table->integer('list_order')->default(0)->change()->comment('排序');
        });
    }

    public function down()
    {
        Schema::table('district_groups', function (Blueprint $table) {
            $table->integer('list_top')->nullable()->change()->comment('置頂');
            $table->integer('list_order')->nullable()->change()->comment('排序');
        });
    }
}
