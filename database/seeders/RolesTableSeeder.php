<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('roles')->delete();
        
        \DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'role_name' => 'Super Admin',
                'role_slug' => 'super-admin',
                'is_user' => 1,
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-30 15:42:13',
                'created_at' => '2025-04-30 05:47:22',
            ),
            1 => 
            array (
                'id' => 2,
                'role_name' => 'Admin',
                'role_slug' => 'admin',
                'is_user' => 1,
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-30 15:40:28',
                'created_at' => '2025-04-30 05:47:44',
            ),
            2 => 
            array (
                'id' => 3,
                'role_name' => 'Finance',
                'role_slug' => 'finance',
                'is_user' => 1,
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-30 15:40:51',
                'created_at' => '2025-04-30 05:47:55',
            ),
            3 => 
            array (
                'id' => 4,
                'role_name' => 'Front Office',
                'role_slug' => 'front-office',
                'is_user' => 1,
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-30 15:41:16',
                'created_at' => '2025-04-30 05:48:06',
            ),
            4 => 
            array (
                'id' => 5,
                'role_name' => 'Reservations',
                'role_slug' => 'reservations',
                'is_user' => 1,
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-30 15:41:34',
                'created_at' => '2025-04-30 05:48:14',
            ),
            5 => 
            array (
                'id' => 6,
                'role_name' => 'Owners',
                'role_slug' => 'owners',
                'is_user' => 1,
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-30 15:41:58',
                'created_at' => '2025-04-30 15:41:58',
            ),
        ));
        
        
    }
}