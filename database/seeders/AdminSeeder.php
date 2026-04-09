<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::query()->find(1) ?? Role::query()->first();

        if (!$role) {
            return;
        }

        $admin = Admin::query()->firstOrNew(['email' => 'admin@gmail.com']);
        $admin->image = 'uploads/images/image.png';
        $admin->name = 'One Kilo Admin';
        $admin->email = 'admin@gmail.com';
        $admin->password = bcrypt('password');
        $admin->role_id = $role->id;
        $admin->status = true;
        $admin->facebook = 'https://facebook.com/onekiloapp';
        $admin->x_url = 'https://x.com/onekiloapp';
        $admin->linkedin = 'https://linkedin.com/company/onekiloapp';
        $admin->whatsapp = '+201000000000';
        $admin->save();


        $adminOneKilo = Admin::query()->firstOrNew(['email' => 'test@brmja.tech']);
        $adminOneKilo->image = 'uploads/images/image.png';
        $adminOneKilo->name = 'One Kilo Admin';
        $adminOneKilo->email = 'test@brmja.tech';
        $adminOneKilo->password = bcrypt('Brmja@102030');
        $adminOneKilo->role_id = $role->id;
        $adminOneKilo->status = true;
        $adminOneKilo->facebook = 'https://facebook.com/onekiloapp';
        $adminOneKilo->x_url = 'https://x.com/onekiloapp';
        $adminOneKilo->linkedin = 'https://linkedin.com/company/onekiloapp';
        $adminOneKilo->whatsapp = '+201000000000';
        $adminOneKilo->save();
    }
}
