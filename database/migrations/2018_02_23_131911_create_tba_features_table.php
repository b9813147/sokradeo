<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbaFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tba_features', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 32)->unique();
            $table->timestamps();
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
        
        Schema::dropIfExists('tba_features');
    }
}
