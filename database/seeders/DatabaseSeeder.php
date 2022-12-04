<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
         \App\Models\User::factory(9)->create();

         \App\Models\User::factory()->create([
             'name' => 'Drian Dirga',
             'email' => 'driandirga@gmail.com',
             'password' => Hash::make('rahasia123'),
         ]);

         $this->call(CompanySeeder::class);
         $this->call(CompanyUserSeeder::class);
         $this->call(TeamSeeder::class);
         $this->call(RoleSeeder::class);
         $this->call(ResponsibilitySeeder::class);
         $this->call(EmployeeSeeder::class);
    }
}
