<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicNoteStatusToGroup extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('public_note_status')->default('1')->after('review_status')->comment('公開點評權限');
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('public_note_status');
        });
    }
}
