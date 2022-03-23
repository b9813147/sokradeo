<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderToTagTypes extends Migration
{
    public function up()
    {
        Schema::table('tag_types', function (Blueprint $table) {
            $table->smallInteger('order')->after('user_id')->comment('排序');
        });
    }

    public function down()
    {
        Schema::table('tag_types', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
