<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RefferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['branch_id' =>1,'type' =>'Doctor','name' => 'Dr murari','designation' => 'hello dr'],
            ['branch_id' =>1,'type' =>'Other','name' => 'khan','designation' => 'hello dr'],
            ['branch_id' =>1,'type' =>'Doctor','name' => 'Dr. Atik','designation' => 'hello dr'],
        ];

        \DB::table('reefers')->insert($categories);
    }
}
