<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['branch_id' =>2,'name' => 'Hematology', 'room_no' => 101, 'room_name' => 'ECG'],
            ['branch_id' =>2,'name' => 'Biochemical', 'room_no' => 101, 'room_name' => 'ECG'],
            ['branch_id' =>2,'name' => 'Electrolytes', 'room_no' => 101, 'room_name' => 'ECG'],
            ['branch_id' =>2,'name' => 'Serology', 'room_no' => 101, 'room_name' => 'ECG'],
            ['branch_id' =>2,'name' => 'Urine', 'room_no' => 102, 'room_name' => 'CBC'],
            ['branch_id' =>2,'name' => 'Stool', 'room_no' => 102, 'room_name' => 'CBC'],
            ['branch_id' =>2,'name' => 'Gram Stain', 'room_no' => 102, 'room_name' => 'CBC'],
            ['branch_id' =>2,'name' => 'Hormone Analysis & Others', 'room_no' => 103, 'room_name' => 'Blood'],
            ['branch_id' =>2,'name' => 'C/S', 'room_no' => 103, 'room_name' => 'Blood'],
            ['branch_id' =>2,'name' => 'Orthopantomogram (OPG)', 'room_no' => 103, 'room_name' => 'Blood'],
            ['branch_id' =>2,'name' => 'X-Ray DR/CR', 'room_no' => 103, 'room_name' => 'Blood'],
            ['branch_id' =>2,'name' => 'X-Ray DR/CR Contrasol', 'room_no' => 103, 'room_name' => 'Blood'],
            ['branch_id' =>2,'name' => 'Ultra Sonogram', 'room_no' => 103, 'room_name' => 'Blood'],
            ['branch_id' =>2,'name' => 'Cardiac', 'room_no' => 101, 'room_name' => 'ECHO'],
            ['branch_id' =>2,'name' => 'Video Endoscopy', 'room_no' => 101, 'room_name' => 'ECHO'],
        ];

        \DB::table('categories')->insert($categories);
    }
}
