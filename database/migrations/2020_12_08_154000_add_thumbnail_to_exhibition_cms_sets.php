<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThumbnailToExhibitionCmsSets extends Migration
{
    public function up()
    {
        Schema::table('exhibition_cms_sets', function (Blueprint $table) {
            $table->string('thumbnail')->after('order')->nullable()->comment('圖片');
        });
    }

    public function down()
    {
        Schema::table('exhibition_cms_sets', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
}
