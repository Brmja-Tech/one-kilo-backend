<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Delivery::create([
            'full_name' => 'Ahmed Ali',
            'phone' => '01123456789',
            'email' => 'ahmed@test.com',
            'password' => bcrypt('123456'),
            'vehicle_type' => 'bike',
            'vehicle_model' => '2022',
            'vehicle_brand' => 'Honda',
            'status' => 'pending'
        ]);

        Delivery::create([
            'full_name' => 'Mohamed Hassan',
            'phone' => '01234567890',
            'email' => 'mohamed@test.com',
            'password' => bcrypt('123456'),
            'vehicle_type' => 'car',
            'vehicle_model' => '2021',
            'vehicle_brand' => 'Toyota',
            'status' => 'approved'
        ]);
    }
}
