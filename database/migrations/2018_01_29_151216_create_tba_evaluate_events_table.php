<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaEvaluateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_evaluate_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tba_id')->unsigned()->index();
            $table->integer('tba_evaluate_user_id'      )->unsigned()->index()->nullable();
            $table->integer('tba_evaluate_event_mode_id')->unsigned()->index();
            $table->integer('user_id' )->unsigned()->index()->nullable();
            $table->integer('group_id')->unsigned()->index()->nullable();
            $table->double('time_point', 12, 5)->unsigned()->nullable();
            $table->text('text')->nullable();
            $table->timestamps();
            
            $table->foreign('tba_id')->references('id')->on('tbas');
            $table->foreign('tba_evaluate_user_id'      )->references('id')->on('tba_evaluate_users');
            $table->foreign('tba_evaluate_event_mode_id')->references('id')->on('tba_evaluate_event_modes');
            $table->foreign('user_id' )->references('id')->on('users');
            $table->foreign('group_id')->references('id')->on('groups');
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
        
        Schema::dropIfExists('tba_evaluate_events');
    }
}
