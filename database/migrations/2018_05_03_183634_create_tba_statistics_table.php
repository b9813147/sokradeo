<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_statistics', function (Blueprint $table) {
            // tba_statistics.id:不需要不應該設置此欄位, 此欄位使用於複雜排序時, 此屬性會與tbas.id混淆(eloquent orm)
            // $table->increments('id');
            //--------------------------
            $table->integer('tba_id')->unsigned()->index();
            $table->tinyInteger('type');
            $table->double('freq',     12, 5)->nullable();
            $table->double('duration', 12, 5)->nullable();
            $table->double('idx',      12, 5)->nullable();
            $table->timestamps();
            
            $table->foreign('tba_id')->references('id')->on('tbas');
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
        
        Schema::dropIfExists('tba_statistics');
    }
}
