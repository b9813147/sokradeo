<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectFieldLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_field_langs', function (Blueprint $table) {
            $table->unsignedInteger('subject_fields_id');
            $table->unsignedInteger('locales_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('locales_id')->references('id')->on('locales');
            $table->foreign('subject_fields_id')->references('id')->on('subject_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subject_field_langs', function (Blueprint $table) {
            $table->dropForeign(['locales_id']);
            $table->dropForeign(['subject_fields_id']);
        });
        Schema::dropIfExists('subject_field_langs');
    }
}
