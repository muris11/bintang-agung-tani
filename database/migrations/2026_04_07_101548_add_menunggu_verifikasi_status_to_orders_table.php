<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only modify ENUM for MySQL - SQLite doesn't support ENUM
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pending',
                'payment_pending',
                'menunggu_verifikasi',
                'processing',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
                'refunded'
            ) DEFAULT 'pending'");
        }
        // For SQLite, no changes needed as it stores enums as strings
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only modify ENUM for MySQL - SQLite doesn't support ENUM
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pending',
                'payment_pending',
                'processing',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
                'refunded'
            ) DEFAULT 'pending'");
        }
        // For SQLite, no changes needed as it stores enums as strings
    }
};
