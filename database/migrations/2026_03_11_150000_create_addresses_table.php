<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('label')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('governorate_id')
                ->nullable()
                ->constrained('governorates')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('region_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->string('street');
            $table->string('building_number')->nullable();
            $table->string('floor')->nullable();
            $table->string('apartment_number')->nullable();
            $table->string('landmark')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['user_id', 'status', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
