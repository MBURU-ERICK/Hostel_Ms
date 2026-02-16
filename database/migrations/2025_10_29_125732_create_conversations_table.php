<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user2_id')->constrained('users')->onDelete('cascade');
            $table->string('conversation_type')->default('direct'); // direct, group, booking, service
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('service_request_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // Ensure unique conversation between two users
            $table->unique(['user1_id', 'user2_id']);
        });

        // Update messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('message_type')->default('text')->after('message');
            $table->string('attachment')->nullable()->after('message_type');

            // Make booking_id nullable since not all messages are related to bookings
            $table->foreignId('booking_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn(['conversation_id', 'message_type', 'attachment']);
        });

        Schema::dropIfExists('conversations');
    }
};
