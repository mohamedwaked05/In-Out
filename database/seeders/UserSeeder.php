<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
  // Manager account
    User::create([
        'name' => 'mhmd Manager',
        'email' => 'manager@example.com',
        'password' => Hash::make('password123'),
        'role' => 'manager',
    ]);

    // Employee accounts
    User::create([
        'name' => 'omar Employee',
        'email' => 'omar@example.com',
        'password' => Hash::make('password123'),
        'role' => 'employee',
    ]);

    User::create([
        'name' => 'moe Employee',
        'email' => 'moe@example.com',
        'password' => Hash::make('password123'),
        'role' => 'employee',
    ]);
    }
}
