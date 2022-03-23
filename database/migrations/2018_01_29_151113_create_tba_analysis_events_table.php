<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaAnalysisEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_analysis_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tba_id')->unsigned()->index();
            $table->integer('tba_analysis_event_mode_id')->unsigned()->index();
            $table->double('time_start', 12, 5)->unsigned()->nullable();
            $table->double('time_end',   12, 5)->unsigned()->nullable();
            $table->double('time_point', 12, 5)->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('tba_id')->references('id')->on('tbas');
            $table->foreign('tba_analysis_event_mode_id')->references('id')->on('tba_analysis_event_modes');
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
        
        Schema::dropIfExists('tba_analysis_events');
    }
}
