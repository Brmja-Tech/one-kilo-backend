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
        Schema::create('variant_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')
                ->constrained('variants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->json('name');
            $table->string('name_plain')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['variant_id', 'name_plain']);
            $table->index(['variant_id', 'status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_items');
    }
};
