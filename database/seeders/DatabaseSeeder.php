<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'Name_User' => 'Admin',
            'Username_User' => 'admin',
            'Password_User' => \Illuminate\Support\Facades\Hash::make('admin123'),
        ]);
    }
}
