<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_role', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('module_id')->unsigned()->index();
            $table->integer('role_id'  )->unsigned()->index();
            $table->timestamps();
            
            $table->foreign('module_id')->references('id')->on('modules');
            $table->foreign('role_id'  )->references('id')->on('roles');
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
        
        Schema::dropIfExists('module_role');
    }
}
