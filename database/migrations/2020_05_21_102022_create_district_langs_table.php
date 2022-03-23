<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district_langs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('districts_id');
            $table->unsignedInteger('locales_id');
            $table->string('name')->nullable()->comment('學區名稱');
            $table->string('description')->nullable()->comment('學區描述');
            $table->timestamps();
            $table->foreign('districts_id')->references('id')->on('districts');
            $table->foreign('locales_id')->references('id')->on('locales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('district_langs', function (Blueprint $table) {
            $table->dropForeign(['districts_id']);
            $table->dropForeign(['locales_id']);
        });

        Schema::dropIfExists('district_langs');
    }
}
