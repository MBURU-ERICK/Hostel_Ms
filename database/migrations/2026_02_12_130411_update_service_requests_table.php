<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {

            if (!Schema::hasColumn('service_requests', 'title')) {
                $table->string('title')->after('service_type')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'address')) {
                $table->string('address')->after('urgency_level')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'room_number')) {
                $table->string('room_number')->after('address')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'preferred_date')) {
                $table->dateTime('preferred_date')->after('room_number')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'estimated_cost')) {
                $table->decimal('estimated_cost', 10, 2)
                      ->after('preferred_date')
                      ->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'actual_cost')) {
                $table->decimal('actual_cost', 10, 2)
                      ->after('estimated_cost')
                      ->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'rated_at')) {
                $table->timestamp('rated_at')
                      ->after('student_review')
                      ->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'address',
                'room_number',
                'preferred_date',
                'estimated_cost',
                'actual_cost',
                'rated_at',
            ]);
        });
    }
};