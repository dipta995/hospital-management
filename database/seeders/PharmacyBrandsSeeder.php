<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PharmacyBrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Square', 'status' => 1],
            ['name' => 'Beximco', 'status' => 1],
            ['name' => 'ACME', 'status' => 1],
        ];

        \DB::table('pharmacy_brands')->insert($brands);
    }
}
