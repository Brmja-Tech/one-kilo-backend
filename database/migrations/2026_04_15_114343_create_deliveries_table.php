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

            Schema::create('deliveries', function (Blueprint $table) {
                $table->id();

                // Basic Info
                $table->string('full_name');
                $table->string('phone')->unique();
                $table->string('email')->nullable();
                $table->string('password');
                $table->text('fcm_token')->nullable();
                $table->string('firebase_uid')->nullable()->unique();
                // Profile
                $table->string('image')->nullable();

                // Vehicle Info
                $table->string('vehicle_type')->nullable(); // car / bike
                $table->string('vehicle_model')->nullable();
                $table->string('vehicle_brand')->nullable();

                // Documents
                $table->string('national_id_image')->nullable();
                $table->string('license_image')->nullable();
                $table->string('vehicle_license_image')->nullable();

                // Status
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

                $table->boolean('login_status')->default(1);

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
