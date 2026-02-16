<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->boolean('is_available')->default(true)->after('is_verified');
            $table->integer('response_time')->nullable()->after('is_available');
            $table->json('coverage_areas')->nullable()->after('response_time');

            // Add indexes for better performance
            $table->index(['service_type', 'is_verified', 'is_available']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropColumn(['is_available', 'response_time', 'coverage_areas']);
            $table->dropIndex(['service_type', 'is_verified', 'is_available']);
            $table->dropIndex(['rating']);
        });
    }
};
