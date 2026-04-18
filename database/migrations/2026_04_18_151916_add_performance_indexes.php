<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add performance indexes to frequently queried columns
     */
    public function up(): void
    {
        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            // Composite index for user order filtering and listing
            $table->index(['user_id', 'status', 'created_at'], 'orders_user_status_created_idx');
            
            // Index for admin dashboard queries by status
            $table->index(['status', 'created_at'], 'orders_status_created_idx');
            
            // Index for date range queries
            $table->index('created_at', 'orders_created_at_idx');
        });

        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            // Composite index for active products with stock
            $table->index(['is_active', 'stock'], 'products_active_stock_idx');
            
            // Index for featured products queries
            $table->index(['is_featured', 'is_active'], 'products_featured_active_idx');
        });

        // Payment proofs table indexes
        Schema::table('payment_proofs', function (Blueprint $table) {
            // Index for pending proofs queue
            $table->index(['status', 'created_at'], 'payment_proofs_status_created_idx');
            
            // Index for order relationship
            $table->index('order_id', 'payment_proofs_order_id_idx');
        });

        // Addresses table indexes
        Schema::table('addresses', function (Blueprint $table) {
            // Composite index for default address lookups
            $table->index(['user_id', 'is_default'], 'addresses_user_default_idx');
        });

        // Stock logs table indexes
        Schema::table('stock_logs', function (Blueprint $table) {
            // Index for product stock history
            $table->index(['product_id', 'created_at'], 'stock_logs_product_created_idx');
        });

        // Cart items table indexes
        Schema::table('cart_items', function (Blueprint $table) {
            // Index for cart lookups
            $table->index(['cart_id', 'product_id'], 'cart_items_cart_product_idx');
        });

        // Order items table indexes
        Schema::table('order_items', function (Blueprint $table) {
            // Index for product sales analytics
            $table->index('product_id', 'order_items_product_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_status_created_idx');
            $table->dropIndex('orders_status_created_idx');
            $table->dropIndex('orders_created_at_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_stock_idx');
            $table->dropIndex('products_featured_active_idx');
        });

        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->dropIndex('payment_proofs_status_created_idx');
            $table->dropIndex('payment_proofs_order_id_idx');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex('addresses_user_default_idx');
        });

        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropIndex('stock_logs_product_created_idx');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_cart_product_idx');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_product_id_idx');
        });
    }
};
