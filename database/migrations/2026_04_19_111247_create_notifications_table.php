<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // polymorphic relation (user or delivery)
            $table->nullableMorphs('notifiable');
            // notifiable_id + notifiable_type

            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // translatable fields
            $table->json('title');
            $table->json('message');


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
