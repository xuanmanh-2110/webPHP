<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'name' => 'Admin Flowershop',
            'email' => 'admin@flowershop.com',
            'password' => Hash::make('admin123'),  // mật khẩu admin123
            'is_admin' => true,
        ]);
    }
}
