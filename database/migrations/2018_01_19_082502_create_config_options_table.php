<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_options', function (Blueprint $table) {
            $table->string('cate',  32);
            $table->string('attr',  32);
            $table->string('param', 32);
            $table->string('name',  32);
            $table->string('description')->nullable();
            
            $table->primary(['cate', 'attr', 'param']);
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
        
        Schema::dropIfExists('config_options');
    }
}
