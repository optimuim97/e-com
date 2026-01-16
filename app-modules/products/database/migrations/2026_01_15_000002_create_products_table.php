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
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            
            // Pricing
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable()->comment('Original price for discount display');
            $table->decimal('cost', 10, 2)->nullable()->comment('Cost price for margin calculation');
            
            // Inventory
            $table->string('sku')->unique()->nullable();
            $table->string('barcode')->unique()->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->boolean('track_inventory')->default(true);
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'on_backorder'])->default('in_stock');
            
            // Physical attributes
            $table->decimal('weight', 8, 2)->nullable()->comment('Weight in kg');
            $table->decimal('length', 8, 2)->nullable()->comment('Length in cm');
            $table->decimal('width', 8, 2)->nullable()->comment('Width in cm');
            $table->decimal('height', 8, 2)->nullable()->comment('Height in cm');
            
            // Status and visibility
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_visible')->default(true);
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // Additional info
            $table->json('attributes')->nullable()->comment('Custom attributes like color, size, etc.');
            $table->integer('view_count')->default(0);
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['slug', 'status', 'is_visible']);
            $table->index(['sku', 'barcode']);
            $table->index('is_featured');
            $table->index('published_at');
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
