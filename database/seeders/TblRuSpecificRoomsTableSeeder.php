<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TblRuSpecificRoomsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tbl_ru_specific_rooms')->delete();
        
        \DB::table('tbl_ru_specific_rooms')->insert(array (
            0 => 
            array (
                'id' => 1,
                'ru_room_id' => 53,
                'ru_room_name' => 'WC',
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-10 10:52:16',
                'created_at' => '2025-04-10 10:52:16',
            ),
            1 => 
            array (
                'id' => 2,
                'ru_room_id' => 81,
                'ru_room_name' => 'Bathroom',
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-10 10:52:16',
                'created_at' => '2025-04-10 10:52:16',
            ),
            2 => 
            array (
                'id' => 3,
                'ru_room_id' => 94,
                'ru_room_name' => 'Kitchen in the living / dining room',
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-10 10:52:16',
                'created_at' => '2025-04-10 10:52:16',
            ),
            3 => 
            array (
                'id' => 4,
                'ru_room_id' => 101,
                'ru_room_name' => 'Kitchen',
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-10 10:52:16',
                'created_at' => '2025-04-10 10:52:16',
            ),
            4 => 
            array (
                'id' => 5,
                'ru_room_id' => 249,
                'ru_room_name' => 'Living room',
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-10 10:52:16',
                'created_at' => '2025-04-10 10:52:16',
            ),
            5 => 
            array (
                'id' => 6,
                'ru_room_id' => 257,
                'ru_room_name' => 'Bedroom',
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-10 10:52:16',
                'created_at' => '2025-04-10 10:52:16',
            ),
            6 => 
            array (
                'id' => 7,
                'ru_room_id' => 372,
                'ru_room_name' => 'Livingroom / Bedroom',
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-10 10:52:16',
                'created_at' => '2025-04-10 10:52:16',
            ),
            7 => 
            array (
                'id' => 8,
                'ru_room_id' => 517,
                'ru_room_name' => 'Bedroom/Living room with kitchen corner',
                'status' => 1,
                'deleted_at' => NULL,
                'updated_at' => '2025-04-10 10:52:16',
                'created_at' => '2025-04-10 10:52:16',
            ),
        ));
        
        
    }
}