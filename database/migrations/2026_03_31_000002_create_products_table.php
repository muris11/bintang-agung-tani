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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(10);
            $table->integer('min_order')->default(1);
            $table->integer('max_order')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->string('unit')->default('pcs');
            $table->decimal('weight', 8, 2)->default(0);
            $table->json('images')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['is_active', 'category_id']);
            $table->index('slug');
            $table->index(['is_featured', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
