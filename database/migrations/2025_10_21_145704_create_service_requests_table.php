<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');
            $table->foreignId('hostel_id')->constrained()->onDelete('cascade');
            $table->string('service_type');
            $table->string('title');
            $table->text('description');
            $table->enum('urgency_level', ['low', 'medium', 'high', 'emergency'])->default('medium');
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('scheduled_date')->nullable();
            $table->dateTime('completed_date')->nullable();
            $table->integer('student_rating')->nullable();
            $table->text('student_review')->nullable();
            $table->decimal('cost', 8, 2)->nullable();
            $table->string('address');
            $table->string('room_number');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_requests');
    }
};
