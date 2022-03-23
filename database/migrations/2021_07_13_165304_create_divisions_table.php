<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDivisionsTable extends Migration
{
    public function up()
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('group_id')->comment('群組ID');
            $table->string('title', 200)->nullable()->comment('組別名稱');

            $table->foreign('group_id')->references('id')->on('groups');
            //

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
        });
        Schema::dropIfExists('divisions');
    }
}
