<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderToExhibitionCmsSets extends Migration
{
    public function up()
    {
        Schema::table('exhibition_cms_sets', function (Blueprint $table) {
            $table->integer('order')->nullable()->after('type')->commend('排序');
        });
    }

    public function down()
    {
        Schema::table('exhibition_cms_sets', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
