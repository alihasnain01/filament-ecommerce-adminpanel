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
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->float('price');
            $table->float('sale_price')->nullable();
            $table->float('cost_per_piece');
            $table->date('discount_start')->nullable();
            $table->date('discount_end')->nullable();
            $table->bigInteger('stock')->nullable();
            $table->integer('allowed_quantity')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_visible')->default(1);
            $table->boolean('is_feature')->default(0);
            $table->date('available_start')->nullable();
            $table->integer('viewed')->default(0);
            $table->integer('ordered')->default(0);
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('slug');
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
