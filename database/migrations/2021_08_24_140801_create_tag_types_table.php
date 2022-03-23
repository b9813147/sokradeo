<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagTypesTable extends Migration
{
    public function up()
    {
        Schema::create('tag_types', function (Blueprint $table) {
            $table->string('id')->unique()->comment('類別ID');
            $table->json('content')->comment('類別名稱');
            $table->unsignedInteger('group_id')->nullable()->comment('頻道ID');
            $table->string('habook_id')->nullable()->comment('用戶ID');

            $table->foreign('group_id')->references('id')->on('groups');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('tag_types', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
        });
        Schema::dropIfExists('tag_types');
    }
}
