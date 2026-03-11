<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('image');
            $table->decimal('price', 10, 2);
            $table->enum('discount_type', ['amount', 'percentage'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->dateTime('discount_starts_at')->nullable();
            $table->dateTime('discount_ends_at')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['category_id', 'status', 'is_featured']);
            $table->index(['status', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
