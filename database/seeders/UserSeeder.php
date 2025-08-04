<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('users')->insert([
            'name' => 'Breno',
            'lastname' => 'Castro',
            'company' => 'V4 Company',
            'phone' => '(62) 99522-5796',
            'email' => 'breno@v4company.com',
            'email_verified_at' => now(),
            'role' => 1,
            'password' => Hash::make('1234'),
        ]);
    }
}
