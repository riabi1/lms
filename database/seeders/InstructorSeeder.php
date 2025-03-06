<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    //
    DB::table('instructors')->insert([
      'name' => 'Instructor',
      'email' => 'instructor@example.com',
      'password' => Hash::make('111'),
    ]);
    }
}
