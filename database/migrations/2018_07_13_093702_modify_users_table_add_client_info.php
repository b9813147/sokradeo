<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyUsersTableAddClientInfo extends Migration
{
    /**
     * Run the migrations.
     * 說明:由只支援HaBook ID變成可支援多種來源
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
        
            $table->string('client_id',   13)->nullable()->after('habook');
            $table->string('client_user', 32)->nullable()->after('client_id');
            $table->string('habook')->nullable()->change();
            
            $table->foreign('client_id')->references('id')->on('clients');
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
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn ('client_id');
            $table->dropColumn ('client_user');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('habook')->nullable(false)->change();
        });
    }
}
