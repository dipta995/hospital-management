<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = Admin::where('email', 'superadmin@email.com')->first();
        if (is_null($user)) {

            $user = new Admin();
            $user->branch_id = 1;
            $user->name = "Super Admin";
            $user->username = "superadmin";
            $user->email = "superadmin@email.com";
            $user->password = Hash::make('12344321');
            $user->save();
        }
    }
}
