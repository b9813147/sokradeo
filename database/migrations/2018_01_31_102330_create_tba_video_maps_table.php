<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaVideoMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_video_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tba_id'  )->unsigned()->index();
            $table->integer('video_id')->unsigned()->index();
            $table->double('tba_start',    12, 5);
            $table->double('tba_end',      12, 5);
            $table->double('video_offset', 12, 5)->unsigned();
            $table->timestamps();
            
            $table->foreign('tba_id'  )->references('id')->on('tbas');
            $table->foreign('video_id')->references('id')->on('videos');
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
        
        Schema::dropIfExists('tba_video_maps');
    }
}
