<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('districts_id');
            $table->unsignedInteger('groups_id');
            $table->integer('list_order')->nullable()->comment('排序');
            $table->integer('list_top')->nullable()->comment('置頂');
            $table->timestamps();

            $table->foreign('districts_id')->references('id')->on('districts');
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
        Schema::table('district_groups', function (Blueprint $table) {
            $table->dropForeign(['districts_id']);
            $table->dropForeign(['groups_id']);
        });

        Schema::dropIfExists('district_groups');
    }
}
