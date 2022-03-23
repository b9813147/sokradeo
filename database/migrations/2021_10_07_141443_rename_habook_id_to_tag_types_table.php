<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameHabookIdToTagTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('tag_types', 'habook_id')) {
            Schema::table('tag_types', function (Blueprint $table) {
                $table->dropColumn('habook_id');
                $table->unsignedInteger('user_id')->nullable()->comment('用戶ID')->after('group_id');
                $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('tag_types', 'user_id')) {
            Schema::table('tag_types', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
                $table->string('habook_id')->nullable()->comment('用戶ID')->after('group_id');
            });
        }
    }
}
