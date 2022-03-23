<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaEvaluateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_evaluate_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tba_id' )->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->char('identity', 1)->nullable();
            $table->timestamps();
            
            $table->foreign('tba_id' )->references('id')->on('tbas');
            $table->foreign('user_id')->references('id')->on('users');
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
        
        Schema::dropIfExists('tba_evaluate_users');
    }
}
