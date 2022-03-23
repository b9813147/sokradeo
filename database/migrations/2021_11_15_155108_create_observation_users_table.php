<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObservationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('observation_users', function (Blueprint $table) {
            // Define fields
            $table->id();
            $table->unsignedBigInteger('observation_class_id')->comment('Foreign key to observation_classes table');
            $table->unsignedInteger('user_id')->comment('Foreign key to users table');
            $table->timestamps();

            // Define relationships
            $table->foreign('observation_class_id')->references('id')->on('observation_classes');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('observation_users', function (Blueprint $table) {
            $table->dropForeign(['observation_class_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('observation_users');
    }
}
