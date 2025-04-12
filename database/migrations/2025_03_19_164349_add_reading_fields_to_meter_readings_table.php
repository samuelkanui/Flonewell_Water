<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            // First, modify the units column to be decimal
            $table->decimal('units', 10, 2)->change();
            
            // Add the new reading fields
            $table->decimal('previous_reading', 10, 2)->after('customer_id');
            $table->decimal('current_reading', 10, 2)->after('previous_reading');
            
            // Update status to be an enum
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            // Revert units back to integer
            $table->integer('units')->change();
            
            // Remove the reading fields
            $table->dropColumn(['previous_reading', 'current_reading']);
            
            // Revert status back to string
            $table->string('status')->default('pending')->change();
        });
    }
};
