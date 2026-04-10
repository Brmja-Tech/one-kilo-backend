<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('governorate_id')
                ->constrained('governorates')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->json('name'); // translatable
            $table->decimal('shipping_price', 10, 2)->default(0);
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['governorate_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
