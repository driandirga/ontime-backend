<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
//        \App\Models\CompanyUser::factory(3)->create();
        for ($i = 0; $i < 20; $i++) {
            DB::table('company_user')->insert([
                'user_id' => rand(1, 10),
                'company_id' => rand(1, 3),
            ]);
        }
    }
}
