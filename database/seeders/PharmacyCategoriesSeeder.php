<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PharmacyCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['branch_id' => 1, 'name' => 'General Medicines', 'description' => 'Common prescription and OTC medicines.'],
            ['branch_id' => 1, 'name' => 'Antibiotics', 'description' => 'Antibiotic drugs and related medicines.'],
            ['branch_id' => 1, 'name' => 'Pediatrics', 'description' => 'Medicines for children and newborns.'],
        ];

        \DB::table('pharmacy_categories')->insert($categories);
    }
}
