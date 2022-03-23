<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyNameCharacterToResources extends Migration
{
    public function up()
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->string('name', 128)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
        });
    }

    public function down()
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->string('name', 128)->charset('utf8')->collation('utf8_unicode_ci')->change();
        });
    }
}
