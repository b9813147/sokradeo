<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlToTbaHistories extends Migration
{
    public function up()
    {
        Schema::table('tba_histories', function (Blueprint $table) {
            $table->string('url')->after('tba_id')->nullable()->comment('連結');
        });
    }

    public function down()
    {
        Schema::table('tba_histories', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
}
