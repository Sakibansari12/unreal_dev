<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TblStatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tbl_states')->delete();
        
        \DB::table('tbl_states')->insert(array (
            0 => 
            array (
                'id' => 1,
                'country_id' => 97,
                'name' => 'Andhra Pradesh',
                'state_code' => 28,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'country_id' => 97,
                'name' => 'Assam',
                'state_code' => 18,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'country_id' => 97,
                'name' => 'Arunachal Pradesh',
                'state_code' => 12,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'country_id' => 97,
                'name' => 'Bihar',
                'state_code' => 10,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'country_id' => 97,
                'name' => 'Jammu & Kashmir',
                'state_code' => 1,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'country_id' => 97,
                'name' => 'Kerala',
                'state_code' => 32,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'country_id' => 97,
                'name' => 'Madhya Pradesh',
                'state_code' => 23,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'country_id' => 97,
                'name' => 'Maharashtra',
                'state_code' => 27,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'country_id' => 97,
                'name' => 'Manipur',
                'state_code' => 14,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'country_id' => 97,
                'name' => 'Meghalaya',
                'state_code' => 17,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'country_id' => 97,
                'name' => 'Mizoram',
                'state_code' => 15,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'country_id' => 97,
                'name' => 'Nagaland',
                'state_code' => 13,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'country_id' => 97,
                'name' => 'Odisha',
                'state_code' => 21,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'country_id' => 97,
                'name' => 'Rajasthan',
                'state_code' => 8,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'country_id' => 97,
                'name' => 'Sikkim',
                'state_code' => 11,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'country_id' => 97,
                'name' => 'Tamil Nadu',
                'state_code' => 33,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'country_id' => 97,
                'name' => 'Tripura',
                'state_code' => 16,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'country_id' => 97,
                'name' => 'Pondicherry',
                'state_code' => 34,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'country_id' => 97,
                'name' => 'Lakshdweep',
                'state_code' => 31,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'country_id' => 97,
                'name' => 'Daman & Diu',
                'state_code' => 25,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'country_id' => 97,
                'name' => 'Dadra & Nagar Haveli',
                'state_code' => 26,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'country_id' => 97,
                'name' => 'Chandigarh',
                'state_code' => 4,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'country_id' => 97,
                'name' => 'Andaman & Nicobar',
                'state_code' => 35,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'country_id' => 97,
                'name' => 'Uttarakhand',
                'state_code' => 5,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'country_id' => 97,
                'name' => 'Jharkhand',
                'state_code' => 20,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'country_id' => 97,
                'name' => 'Chattisgarh',
                'state_code' => 22,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'country_id' => 97,
                'name' => 'Delhi',
                'state_code' => 7,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'country_id' => 97,
                'name' => 'Punjab',
                'state_code' => 3,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'country_id' => 97,
                'name' => 'Karnataka',
                'state_code' => 29,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'country_id' => 97,
                'name' => 'Gujarat',
                'state_code' => 24,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'country_id' => 97,
                'name' => 'Uttar Pradesh',
                'state_code' => 9,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'country_id' => 97,
                'name' => 'Haryana',
                'state_code' => 6,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'country_id' => 97,
                'name' => 'Goa',
                'state_code' => 30,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'country_id' => 97,
                'name' => 'West Bengal',
                'state_code' => 19,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'country_id' => 97,
                'name' => 'Himachal Pradesh',
                'state_code' => 2,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'country_id' => 97,
                'name' => 'Telangana',
                'state_code' => 36,
                'status' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}