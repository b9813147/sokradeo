<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_user', function (Blueprint $table) {
            //$table->increments('id');
            $table->integer('group_id')->unsigned()->index();
            $table->integer('user_id' )->unsigned()->index();
            $table->tinyInteger('member_status')->default('0');
            $table->string('member_duty', 32)->default('');
            $table->timestamps();
            
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('user_id' )->references('id')->on('users');
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
        
        Schema::dropIfExists('group_user');
    }
}
