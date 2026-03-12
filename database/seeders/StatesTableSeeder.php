<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('states')->delete();
        
        \DB::table('states')->insert(array (
            0 => 
            array (
                'id' => 1,
                'state_name' => 'ANDAMAN AND NICOBAR ISLANDS',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'state_name' => 'ANDHRA PRADESH',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'state_name' => 'ARUNACHAL PRADESH',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'state_name' => 'ASSAM',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'state_name' => 'BIHAR',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'state_name' => 'CHATTISGARH',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'state_name' => 'CHANDIGARH',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'state_name' => 'DAMAN AND DIU',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'state_name' => 'DELHI',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'state_name' => 'DADRA AND NAGAR HAVELI',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'state_name' => 'GOA',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'state_name' => 'GUJARAT',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'state_name' => 'HIMACHAL PRADESH',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'state_name' => 'HARYANA',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'state_name' => 'JAMMU AND KASHMIR',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'state_name' => 'JHARKHAND',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'state_name' => 'KERALA',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'state_name' => 'KARNATAKA',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'state_name' => 'LAKSHADWEEP',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'state_name' => 'MEGHALAYA',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'state_name' => 'MAHARASHTRA',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'state_name' => 'MANIPUR',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'state_name' => 'MADHYA PRADESH',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'state_name' => 'MIZORAM',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'state_name' => 'NAGALAND',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'state_name' => 'ORISSA',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'state_name' => 'PUNJAB',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'state_name' => 'PONDICHERRY',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'state_name' => 'RAJASTHAN',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'state_name' => 'SIKKIM',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'state_name' => 'TELANGANA',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => '2024-09-17 11:49:03',
            ),
            31 => 
            array (
                'id' => 32,
                'state_name' => 'TAMIL NADU',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => '2024-09-17 11:42:44',
            ),
            32 => 
            array (
                'id' => 33,
                'state_name' => 'TRIPURA',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => '2024-09-17 11:50:07',
            ),
            33 => 
            array (
                'id' => 34,
                'state_name' => 'UTTAR PRADESH',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'state_name' => 'UTTARAKHAND',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => '2024-09-17 11:43:13',
            ),
            35 => 
            array (
                'id' => 36,
                'state_name' => 'WEST BENGAL',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-04-30 13:13:13',
                'updated_at' => '2024-09-17 11:47:05',
            ),
        ));
        
        
    }
}