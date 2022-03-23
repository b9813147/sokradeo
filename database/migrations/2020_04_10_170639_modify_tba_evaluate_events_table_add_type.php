<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTbaEvaluateEventsTableAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tba_evaluate_events', function (Blueprint $table) {
            $table->tinyInteger('evaluate_type')->after('text')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tba_evaluate_events', function (Blueprint $table) {
            $table->dropColumn('evaluate_type');
        });
    }
}
