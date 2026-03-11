<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = array_keys(config('permessions_en'));

        Role::query()->updateOrCreate(
            ['id' => 1],
            [
                'role' => [
                    'ar' => 'مدير النظام',
                    'en' => 'Super Admin',
                ],
                'permession' => json_encode($permissions, JSON_UNESCAPED_UNICODE),
            ]
        );
    }
}
