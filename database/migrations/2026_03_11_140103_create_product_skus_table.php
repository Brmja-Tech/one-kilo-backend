<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('signature');
            $table->string('sku')->nullable()->unique();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity')->default(0);
            $table->boolean('status')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();

            $table->unique(['product_id', 'signature']);
            $table->index(['product_id', 'status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_skus');
    }
};
