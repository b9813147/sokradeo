<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyGroupChannelContentsTableAddContentUpdateLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_channel_contents', function (Blueprint $table) {
            $table->tinyInteger('content_update_limit')->after('content_public')->default(0)->comment('限制課例上傳的更新 1=啟用 0=停用');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_channel_contents', function (Blueprint $table) {
            $table->dropColumn('content_update_limit');
        });
    }
}
