<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAbbrToGroups extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('abbr', 40)->nullable()->after('school_code')->comment('簡碼');
            $table->string('country_code', 20)->nullable()->after('public_note_status')->comment('國碼');
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('abbr');
            $table->dropColumn('country_code');
        });
    }
}
