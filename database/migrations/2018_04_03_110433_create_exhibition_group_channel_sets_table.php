<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExhibitionGroupChannelSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exhibition_group_channel_sets', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('group_channel_id')->unsigned()->index();
            $table->string('type', 32);
            $table->timestamps();
            
            $table->foreign('group_channel_id')->references('id')->on('group_channels');
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
        
        Schema::dropIfExists('exhibition_group_channel_sets');
    }
}
