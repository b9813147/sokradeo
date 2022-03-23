<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyEvalEventFileAddImageUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tba_evaluate_event_files', function (Blueprint $table) {

            $table->string('name', 32)->nullable()->change();
            $table->string('ext',  16)->nullable()->change();
            $table->text('image_url')->nullable()->after('ext');
            $table->text('preview_url')->nullable()->after('image_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (env('APP_ENV', 'production') === 'production') {
            throw new \Exception('no migration rollback in production app env');
            return;
        }

        Schema::table('tba_evaluate_event_files', function (Blueprint $table) {

            $table->string('name', 32)->nullable(false)->change();
            $table->string('ext',  16)->nullable(false)->change();
            $table->dropColumn('image_url');
            $table->dropColumn('preview_url');
        });
    }
}
