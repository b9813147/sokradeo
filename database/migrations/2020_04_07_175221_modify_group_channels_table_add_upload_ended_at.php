<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyGroupChannelsTableAddUploadEndedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_channels', function (Blueprint $table) {
            $table->dateTime('upload_ended_at')->after('upload_limit')->nullable()->comment('頻道課例上傳截止時間');
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
            $table->dropColumn('upload_ended_at');
        });
    }
}
