<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOrderToGroupSubjectFields extends Migration
{
    public function up()
    {
        Schema::table('group_subject_fields', function (Blueprint $table) {
            $table->integer('order')->default(0)->change()->comment('排序');
        });
    }

    public function down()
    {
        Schema::table('group_subject_fields', function (Blueprint $table) {
            $table->integer('order')->after('subject_fields_id')->nullable()->change();
        });
    }
}
