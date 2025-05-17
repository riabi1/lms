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
        Schema::table('orders', function (Blueprint $table) {
            // Drop the unique index on payment_id
            $table->dropUnique(['payment_id']);
            
            // Add a composite unique index on user_id, course_id, and payment_id
            $table->unique(['user_id', 'course_id', 'payment_id'], 'orders_user_course_payment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the composite unique index
            $table->dropUnique('orders_user_course_payment_unique');
            
            // Restore the unique index on payment_id
            $table->unique('payment_id');
        });
    }
};