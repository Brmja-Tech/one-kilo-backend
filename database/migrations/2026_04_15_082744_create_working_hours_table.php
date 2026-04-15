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
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger('day_of_week');

            $table->json('day_name'); // {"en":"Sunday","ar":"الاحد"}


            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();

            $table->enum('status', ['open', 'close', 'busy'])->default('open');

            $table->timestamps();

            $table->unique('day_of_week');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};
