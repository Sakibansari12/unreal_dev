<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TblGstsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tbl_gsts')->delete();
        
        \DB::table('tbl_gsts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'slabs_start' => 1,
                'slabs_upto' => 7500,
                'gst_percentage' => 5.0,
                'status' => 1,
                'position' => 0,
                'add_ip' => '183.83.221.105',
                'add_by' => 'Super Admin',
                'update_ip' => NULL,
                'update_by' => NULL,
                'created_at' => '2025-10-24 10:38:14',
                'updated_at' => '2025-10-24 10:38:14',
            ),
            1 => 
            array (
                'id' => 2,
                'slabs_start' => 7501,
                'slabs_upto' => 5000000,
                'gst_percentage' => 18.0,
                'status' => 1,
                'position' => 0,
                'add_ip' => '183.83.221.105',
                'add_by' => 'Super Admin',
                'update_ip' => NULL,
                'update_by' => NULL,
                'created_at' => '2025-10-24 10:38:14',
                'updated_at' => '2025-10-24 10:38:14',
            ),
        ));
        
        
    }
}