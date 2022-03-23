<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaTbaFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_tba_feature', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('tba_id'        )->unsigned()->index();
            $table->integer('tba_feature_id')->unsigned()->index();
            $table->timestamps();
            
            $table->foreign('tba_id'        )->references('id')->on('tbas');
            $table->foreign('tba_feature_id')->references('id')->on('tba_features');
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
        
        Schema::dropIfExists('tba_tba_feature');
    }
}
