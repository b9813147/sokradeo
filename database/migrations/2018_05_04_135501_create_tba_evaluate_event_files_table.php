<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaEvaluateEventFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_evaluate_event_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tba_evaluate_event_id')->unsigned()->index();
            $table->string('name', 32);
            $table->string('ext',  16);
            $table->timestamps();
            
            $table->foreign('tba_evaluate_event_id')->references('id')->on('tba_evaluate_events');
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
        
        Schema::dropIfExists('tba_evaluate_event_files');
    }
}
