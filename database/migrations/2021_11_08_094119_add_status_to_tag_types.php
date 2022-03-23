<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToTagTypes extends Migration
{
    public function up()
    {
        Schema::table('tag_types', function (Blueprint $table) {
            $table->addColumn('boolean', 'status')->default(1)->after('order')->comment('標籤狀態');
        });
    }

    public function down()
    {
        Schema::table('tag_types', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
