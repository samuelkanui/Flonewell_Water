<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('phone');
            $table->index('role');
            $table->index('agent_id');
        });

        // Add indexes to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('mpesa_transaction_id');
            $table->index('created_at');
        });

        // Add indexes to meter_readings table
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('agent_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down()
    {
        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            $table->dropIndex(['role']);
            $table->dropIndex(['agent_id']);
        });

        // Remove indexes from payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['mpesa_transaction_id']);
            $table->dropIndex(['created_at']);
        });

        // Remove indexes from meter_readings table
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['agent_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
}; 