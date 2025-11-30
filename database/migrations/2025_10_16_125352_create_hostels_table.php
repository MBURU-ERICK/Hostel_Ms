<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location');
            $table->text('address');
            $table->decimal('rent_per_month', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->integer('rooms_available');
            $table->integer('total_rooms');
            $table->json('amenities')->nullable();
            $table->text('rules')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_available')->default(true);
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostels');
    }
};
