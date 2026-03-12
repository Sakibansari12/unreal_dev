<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Plain password (for your reference / login)
        $plainPassword = 'Newpass@2026';

        
        DB::table('admins')->delete();

        DB::table('admins')->insert([
            [
                'id' => 1,
                'parent_user_id' => 1,
                'name' => 'Super Admin',
                'email' => 'admin@unreal.in',
                'mobile_no' => '9876543212',
                'role' => 'Super Admin',
                'role_id' => 1,
                'status' => 1,
                'password' => Hash::make($plainPassword),
                'contact_person' => null,
                'address' => null,
                'state_id' => null,
                'state_name' => null,
                'city_id' => null,
                'city_name' => null,
                'gst' => null,
                'sale_plan' => null,
                'note' => null,
                'discount' => null,
                'remember_token' => null,
                'created_at' => '2025-02-13 23:42:29',
                'updated_at' => '2025-05-02 12:55:39',
            ],
        ]);
    }
}
