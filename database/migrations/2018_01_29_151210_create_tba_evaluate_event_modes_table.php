<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaEvaluateEventModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_evaluate_event_modes', function (Blueprint $table) {
            $table->increments('id');
            $table->char('identity', 1);
            $table->string('event', 32);
            $table->string('mode',  32)->nullable();
            $table->tinyInteger('type');
            $table->string('color', 10)->nullable();
            $table->string('style', 16)->nullable();
            $table->integer('order')->unsigned()->nullable();
            $table->timestamps();
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
        
        Schema::dropIfExists('tba_evaluate_event_modes');
    }
}
