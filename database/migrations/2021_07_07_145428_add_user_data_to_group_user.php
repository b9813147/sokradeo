<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserDataToGroupUser extends Migration
{
    public function up()
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->json('user_data')->after('member_duty')->default(new Expression('(JSON_OBJECT())'))->comment('使用者資訊');
        });
    }

    public function down()
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->dropColumn('user_data');
        });
    }
}
