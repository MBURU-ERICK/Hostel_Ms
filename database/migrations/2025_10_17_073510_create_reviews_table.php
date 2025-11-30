<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('hostel_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->unsigned()->between(1, 5);
            $table->text('comment');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            // Ensure a user can only review a hostel once per booking
            $table->unique(['user_id', 'hostel_id', 'booking_id']);
            
            // Indexes for better performance
            $table->index(['hostel_id', 'is_approved']);
            $table->index(['user_id', 'hostel_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
