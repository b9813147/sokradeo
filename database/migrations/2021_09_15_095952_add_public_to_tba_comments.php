<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicToTbaComments extends Migration
{
    public function up()
    {
        Schema::table('tba_comments', function (Blueprint $table) {
            $table->boolean('public')->after('comment_type')->default(false)->comment('判斷是否為公開標記');
            $table->text('text')->nullable()->change();
            $table->unsignedInteger('user_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('tba_comments', function (Blueprint $table) {
            $table->dropColumn('public');
            $table->text('text')->change();
            $table->unsignedInteger('user_id')->change();
        });
    }
}
