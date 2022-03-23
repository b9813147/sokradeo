<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTbasTableAddPlaylisted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
        
            $table->boolean('playlisted')->default('0')->after('hits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (env('APP_ENV', 'production') === 'production') {
            throw new \Exception('no migration rollback in production app env');
        }
        
        Schema::table('tbas', function (Blueprint $table) {
            
            $table->dropColumn('playlisted');
        });
    }
}
