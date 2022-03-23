<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id'    )->unsigned()->index();
            $table->integer('resource_id')->unsigned()->index();
            $table->string('name', 128);
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('hits')->unsigned()->default('0');
            $table->timestamps();
            
            $table->foreign('user_id'    )->references('id')->on('users');
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
        
        Schema::dropIfExists('videos');
    }
}
