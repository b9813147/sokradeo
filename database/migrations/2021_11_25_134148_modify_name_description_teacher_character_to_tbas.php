<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyNameDescriptionTeacherCharacterToTbas extends Migration
{
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->string('name', 128)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->text('description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->string('teacher', 32)->nullable()->after('updated_at')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
        });
    }

    public function down()
    {
        Schema::table('tbas', function (Blueprint $table) {
            $table->string('name', 128)->charset('utf8')->collation('utf8_unicode_ci')->change();
            $table->text('description')->nullable()->charset('utf8')->collation('utf8_unicode_ci')->change();
            $table->string('teacher', 32)->nullable()->after('updated_at')->charset('utf8')->collation('utf8_unicode_ci')->change();
        });
    }
}
