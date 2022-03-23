<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->string('id')->unique()->comment('標籤序號');
            $table->json('content')->comment('標籤名稱');
            $table->string('type_id')->comment('類別關聯');
            $table->tinyInteger('is_positive')->default(1)->comment('判斷正向反向');
            $table->foreign('type_id')->references('id')->on('tag_types');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
        });
        Schema::dropIfExists('tags');
    }
}
