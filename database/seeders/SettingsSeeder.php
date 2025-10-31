<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            $settings = [
                ['branch_id' =>1,'key' => 'company_name', 'value' => 'Your Company Name'],
                ['branch_id' =>1,'key' => 'logo', 'value' => 'logo.png'],
                ['branch_id' =>1,'key' => 'email', 'value' => 'contact@yourcompany.com'],
                ['branch_id' =>1,'key' => 'phone_one', 'value' => '+1234567890'],
                ['branch_id' =>1,'key' => 'phone_two', 'value' => '+1234567890'],
                ['branch_id' =>1,'key' => 'address', 'value' => 'Your Address Here'],
            ];

            foreach ($settings as $setting) {
                Setting::updateOrCreate(
                    ['branch_id' => $setting['branch_id'], 'key' => $setting['key']], // Conditions to match
                    ['value' => $setting['value']]
                );
            }
    }
}
