<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    //
    DB::table('admins')->insert([
      'name' => 'Admin',
      'email' => 'admin@example.com',
      'password' => Hash::make('111'),
    ]);
    }
}
