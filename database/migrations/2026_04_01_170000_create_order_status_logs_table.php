<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by_admin_id')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('new_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
