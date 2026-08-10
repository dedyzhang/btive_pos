<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FirstUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('Admin.10014022026888');
        User::create([
            'name' => 'Admin',
            'role' => 'admin',
            'username' => '10014022026888',
            'password' => $password,
            'token' => '1',
            'profile_picture' => null,
        ]);
    }
}
