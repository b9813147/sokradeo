<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyConfigParametersTableReviseValType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('config_parameters', function (Blueprint $table) {
        
            $table->text('val')->nullable()->change();
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
        
        Schema::table('config_parameters', function (Blueprint $table) {
        
            $table->string('val', 255)->nullable()->change();
        });
    }
}
