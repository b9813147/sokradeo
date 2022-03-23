<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupChannelContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_channel_contents', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('group_id'        )->unsigned()->index();
            $table->integer('group_channel_id')->unsigned()->index();
            $table->morphs('content');
            $table->tinyInteger('content_status')->default('0');
            $table->boolean    ('content_public')->default('0');
            $table->timestamps();
            
            $table->foreign('group_id'        )->references('id')->on('groups');
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
        
        Schema::dropIfExists('group_channel_contents');
    }
}
