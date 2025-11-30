<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['student', 'landlord', 'service_provider'])->default('student');
            $table->boolean('is_approved')->default(false);
            $table->string('phone')->nullable();
        });

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('admission_number')->unique();
            $table->string('id_number');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('institution_name');
            $table->string('course');
            $table->string('year_of_study');
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'is_approved', 'phone']);
        });
    }
};
