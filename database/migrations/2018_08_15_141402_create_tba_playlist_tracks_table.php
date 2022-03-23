<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaPlaylistTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_playlist_tracks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tba_id'    )->unsigned()->index();
            $table->integer('ref_tba_id')->unsigned();
            $table->integer('list_order')->unsigned()->default('0');
            $table->double('time_start', 12, 5)->unsigned()->nullable();
            $table->double('time_end',   12, 5)->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('tba_id')->references('id')->on('tbas');
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
        
        Schema::dropIfExists('tba_playlist_tracks');
    }
}
