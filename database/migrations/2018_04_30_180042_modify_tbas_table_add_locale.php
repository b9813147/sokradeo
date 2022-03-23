<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTbasTableAddLocale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
        
            $table->integer('locale_id')->unsigned()->index()->nullable();
            $table->string('mark', 64)->nullable(); // 暫時欄位, 標記IES EXNO
            
            $table->foreign('locale_id')->references('id')->on('locales');
        
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
            $table->dropForeign(['locale_id']);
            $table->dropIndex  (['locale_id']);
            $table->dropColumn ('locale_id');
        });
        
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('mark');
        });
    }
}
