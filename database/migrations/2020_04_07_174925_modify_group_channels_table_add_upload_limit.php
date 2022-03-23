<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyGroupChannelsTableAddUploadLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_channels', function (Blueprint $table) {
            $table->tinyInteger('upload_limit')->after('public')->default(0)->comment('限制頻道課例上傳 1=啟用 0=停用');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_channels', function (Blueprint $table) {
            $table->dropColumn('upload_limit');
        });
    }
}
