<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeToTbaAnnexes extends Migration
{
    public function up()
    {
        Schema::table('tba_annexes', function (Blueprint $table) {
            $table->addColumn('integer', 'size')->after('type');
        });
    }

    public function down()
    {
        Schema::table('tba_annexes', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
}
