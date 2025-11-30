<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('hostels', function (Blueprint $table) {
        $table->string('room_type')->default('shared')->after('rooms_available'); // shared, single, apartment
        $table->boolean('instant_booking')->default(false)->after('is_available');
        // Add more fields as needed
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hostels', function (Blueprint $table) {
            //
        });
    }
};
