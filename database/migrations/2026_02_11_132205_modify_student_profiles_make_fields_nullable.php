<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyStudentProfilesMakeFieldsNullable extends Migration
{
    public function up()
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            // Make all fields nullable except user_id
            $table->string('admission_number')->nullable()->change();
            $table->string('id_number')->nullable()->change();
            $table->string('gender')->nullable()->change();
            $table->string('institution_name')->nullable()->change();
            $table->string('course')->nullable()->change();
            $table->string('year_of_study')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('emergency_contact_name')->nullable()->change();
            $table->string('emergency_contact_phone')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            // Revert back to NOT NULL (this might fail if there are null values)
            $table->string('admission_number')->nullable(false)->change();
            $table->string('id_number')->nullable(false)->change();
            $table->string('gender')->nullable(false)->change();
            $table->string('institution_name')->nullable(false)->change();
            $table->string('course')->nullable(false)->change();
            $table->string('year_of_study')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->string('emergency_contact_name')->nullable(false)->change();
            $table->string('emergency_contact_phone')->nullable(false)->change();
        });
    }
}