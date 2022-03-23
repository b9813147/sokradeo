<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;

class ModifyTbaEvaluateEventsTimePoint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }
        Schema::table('tba_evaluate_events', function (Blueprint $table) {
            $table->double('time_point', 12, 5)->unsigned(false)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     * @throws Exception
     */
    public function down()
    {
        if (env('APP_ENV', 'production') === 'production') {
            throw new \Exception('no migration rollback in production app env');
        }

        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }
        Schema::table('tba_evaluate_events', function (Blueprint $table) {
            $table->double('time_point', 12, 5)->unsigned()->nullable()->change();
        });
    }
}
