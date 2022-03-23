<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTbasTableAddEduStage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
        
            $table->integer('educational_stage_id')->unsigned()->index()->nullable()->after('subject');
            
            $table->foreign('educational_stage_id')->references('id')->on('educational_stages');
        
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
        }
        
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropForeign(['educational_stage_id']);
            $table->dropIndex  (['educational_stage_id']);
            $table->dropColumn ('educational_stage_id');
        });
    }
}
