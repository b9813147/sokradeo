<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('groups_id')->nullable();
            $table->integer('type')->nullable()->comment('評比等級');
            $table->text('name')->nullable()->comment('評比名稱');
            $table->timestamps();
            $table->foreign('groups_id')->references('id')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign(['groups_id']);
            $table->dropColumn('groups_id');
        });
        Schema::dropIfExists('ratings');
    }
}
