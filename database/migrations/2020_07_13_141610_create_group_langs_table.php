<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_langs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200)->nullable()->comment('名稱');
            $table->string('description', 200)->nullable()->comment('描述');
            $table->unsignedInteger('groups_id')->nullable();
            $table->unsignedInteger('locales_id')->nullable();
            $table->timestamps();

            $table->foreign('groups_id')->references('id')->on('groups');
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
        Schema::table('group_langs', function (Blueprint $table) {
            $table->dropForeign(['groups_id']);
            $table->dropForeign(['locales_id']);
        });
        Schema::dropIfExists('group_langs');
    }
}
