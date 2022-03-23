<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyNameDescriptionAuthorCopyrightToVideos extends Migration
{
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('name', 128)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->text('description')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->string('author', 32)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
            $table->string('copyright', 32)->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->change();
        });
    }

    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('name', 128)->charset('utf8')->collation('utf8_unicode_ci')->change();
            $table->text('description')->nullable()->charset('utf8')->collation('utf8_unicode_ci')->change();
            $table->string('author', 32)->nullable()->charset('utf8')->collation('utf8_unicode_ci')->change();
            $table->string('copyright', 32)->nullable()->charset('utf8')->collation('utf8_unicode_ci')->change();
        });
    }
}
