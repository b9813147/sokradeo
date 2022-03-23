<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBindingNumberToTbas extends Migration
{
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->string('binding_number', 150)->after('mark')->nullable()->comment('綁定編號');
        });
    }

    public function down()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('binding_number');
        });
    }
}
