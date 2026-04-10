<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_sku_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_sku_id')
                ->constrained('product_skus')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('variant_id')
                ->constrained('variants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('variant_item_id')
                ->constrained('variant_items')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['product_sku_id', 'variant_id'], 'product_sku_item_unique');
            $table->index(['variant_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sku_items');
    }
};
