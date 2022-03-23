<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaAnnexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_annexes', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('tba_id')->unsigned()->index();
            $table->integer('resource_id')->unsigned()->index();
            $table->string('type', 32);
            $table->timestamps();
            
            $table->foreign('tba_id'     )->references('id')->on('tbas');
            $table->foreign('resource_id')->references('id')->on('resources');
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
        
        Schema::dropIfExists('tba_annexes');
    }
}
