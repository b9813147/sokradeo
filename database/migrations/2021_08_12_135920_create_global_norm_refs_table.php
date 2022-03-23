<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateGlobalNormRefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the global norm reference table
        Schema::create('global_norm_refs', function (Blueprint $table) {
            $table->increments('id');
            $table->year('year')->comment('year of analyzed data');
            $table->float('p1')->default(0)->comment('statistics type 49');
            $table->float('p2')->default(0)->comment('statistics type 50');
            $table->float('p3')->default(0)->comment('statistics type 53');
            $table->float('p4')->default(0)->comment('statistics type 61');
            $table->float('p5')->default(0)->comment('statistics type 52');
            $table->float('p6')->default(0)->comment('statistics type 54');
            $table->float('freq')->default(0)->comment('statistics type 31');
            $table->float('tech_interact')->default(0)->comment('statistics type 47');
            $table->float('peda_app')->default(0)->comment('statistics type 48');
            $table->float('feedback_avg')->default(0)->comment('irs count divided by student count');
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
        Schema::dropIfExists('global_norm_refs');
    }
}
