<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'image'                => 'uploads/images/image.png',
            'name'                 => 'User',
            'email'                => 'user@gmail.com',
            'phone'                => '1234567890',
            'email_verified_at'    => now(),
            'password'             => bcrypt('password'),
        ]);
    }
}
