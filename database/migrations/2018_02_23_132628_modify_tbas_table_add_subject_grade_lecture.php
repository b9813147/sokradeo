<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTbasTableAddSubjectGradeLecture extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbas', function (Blueprint $table) {
            
            $table->integer('subject_field_id')->unsigned()->index()->nullable();
            $table->string ('subject', 32 )->nullable();
            $table->integer('grade'       )->unsigned()->nullable();
            $table->integer('lecture_type')->unsigned()->nullable();
            $table->date   ('lecture_date')->nullable();
            
            $table->foreign('subject_field_id')->references('id')->on('subject_fields');
            
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
            $table->dropForeign(['subject_field_id']);
            $table->dropIndex  (['subject_field_id']);
            $table->dropColumn ('subject_field_id');
        });
        
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('subject');
        });
        
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('grade');
        });
        
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('lecture_type');
        });
        
        Schema::table('tbas', function (Blueprint $table) {
            $table->dropColumn('lecture_date');
        });
            
    }
}
