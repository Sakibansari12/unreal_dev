<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cities')->delete();
        
        \DB::table('cities')->insert(array (
            0 => 
            array (
                'id' => 1,
                'state_id' => 1,
                'city_name' => 'Nicobar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'state_id' => 1,
                'city_name' => 'North and Middle Andaman',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'state_id' => 1,
                'city_name' => 'South Andaman',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'state_id' => 2,
                'city_name' => 'Anantapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'state_id' => 2,
                'city_name' => 'Chittoor',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'state_id' => 2,
                'city_name' => 'East Godavari',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'state_id' => 2,
                'city_name' => 'Guntur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'state_id' => 2,
                'city_name' => 'Krishna',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'state_id' => 2,
                'city_name' => 'Kurnool',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'state_id' => 2,
                'city_name' => 'Prakasam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'state_id' => 2,
                'city_name' => 'Srikakulam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'state_id' => 2,
                'city_name' => 'Sri Potti Sriramulu Nellore',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'state_id' => 2,
                'city_name' => 'Visakhapatnam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'state_id' => 2,
                'city_name' => 'Vizianagaram',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'state_id' => 2,
                'city_name' => 'West Godavari',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'state_id' => 2,
            'city_name' => 'YSR District, Kadapa (Cuddapah)',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'state_id' => 3,
                'city_name' => 'Anjaw',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'state_id' => 3,
                'city_name' => 'Changlang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'state_id' => 3,
                'city_name' => 'Dibang Valley',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'state_id' => 3,
                'city_name' => 'East Kameng',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'state_id' => 3,
                'city_name' => 'East Siang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            21 => 
            array (
                'id' => 22,
                'state_id' => 3,
                'city_name' => 'Kra Daadi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            22 => 
            array (
                'id' => 23,
                'state_id' => 3,
                'city_name' => 'Kurung Kumey',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            23 => 
            array (
                'id' => 24,
                'state_id' => 3,
                'city_name' => 'Lepa Rada',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            24 => 
            array (
                'id' => 25,
                'state_id' => 3,
                'city_name' => 'Lohit',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            25 => 
            array (
                'id' => 26,
                'state_id' => 3,
                'city_name' => 'Longding',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            26 => 
            array (
                'id' => 27,
                'state_id' => 3,
                'city_name' => 'Lower Dibang Valley',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            27 => 
            array (
                'id' => 28,
                'state_id' => 3,
                'city_name' => 'Lower Siang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            28 => 
            array (
                'id' => 29,
                'state_id' => 3,
                'city_name' => 'Lower Subansiri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            29 => 
            array (
                'id' => 30,
                'state_id' => 3,
                'city_name' => 'Namsai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            30 => 
            array (
                'id' => 31,
                'state_id' => 3,
                'city_name' => 'Pakke Kessang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            31 => 
            array (
                'id' => 32,
                'state_id' => 3,
                'city_name' => 'Papum Pare',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            32 => 
            array (
                'id' => 33,
                'state_id' => 3,
                'city_name' => 'Shi Yomi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            33 => 
            array (
                'id' => 34,
                'state_id' => 3,
                'city_name' => 'Siang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            34 => 
            array (
                'id' => 35,
                'state_id' => 3,
                'city_name' => 'Tawang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            35 => 
            array (
                'id' => 36,
                'state_id' => 3,
                'city_name' => 'Tirap',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            36 => 
            array (
                'id' => 37,
                'state_id' => 3,
                'city_name' => 'Upper Siang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            37 => 
            array (
                'id' => 38,
                'state_id' => 3,
                'city_name' => 'Upper Subansiri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            38 => 
            array (
                'id' => 39,
                'state_id' => 3,
                'city_name' => 'West Kameng',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            39 => 
            array (
                'id' => 40,
                'state_id' => 3,
                'city_name' => 'West Siang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            40 => 
            array (
                'id' => 41,
                'state_id' => 4,
                'city_name' => 'Baksa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            41 => 
            array (
                'id' => 42,
                'state_id' => 4,
                'city_name' => 'Barpeta',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            42 => 
            array (
                'id' => 43,
                'state_id' => 4,
                'city_name' => 'Biswanath',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            43 => 
            array (
                'id' => 44,
                'state_id' => 4,
                'city_name' => 'Bongaigaon',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            44 => 
            array (
                'id' => 45,
                'state_id' => 4,
                'city_name' => 'Cachar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            45 => 
            array (
                'id' => 46,
                'state_id' => 4,
                'city_name' => 'Charaideo',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            46 => 
            array (
                'id' => 47,
                'state_id' => 4,
                'city_name' => 'Chirang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            47 => 
            array (
                'id' => 48,
                'state_id' => 4,
                'city_name' => 'Darrang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            48 => 
            array (
                'id' => 49,
                'state_id' => 4,
                'city_name' => 'Dhemaji',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            49 => 
            array (
                'id' => 50,
                'state_id' => 4,
                'city_name' => 'Dhubri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            50 => 
            array (
                'id' => 51,
                'state_id' => 4,
                'city_name' => 'Dibrugarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            51 => 
            array (
                'id' => 52,
                'state_id' => 4,
                'city_name' => 'Dima Hasao',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            52 => 
            array (
                'id' => 53,
                'state_id' => 4,
                'city_name' => 'Goalpara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            53 => 
            array (
                'id' => 54,
                'state_id' => 4,
                'city_name' => 'Golaghat',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            54 => 
            array (
                'id' => 55,
                'state_id' => 4,
                'city_name' => 'Hailakandi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            55 => 
            array (
                'id' => 56,
                'state_id' => 4,
                'city_name' => 'Hojai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            56 => 
            array (
                'id' => 57,
                'state_id' => 4,
                'city_name' => 'Jorhat',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            57 => 
            array (
                'id' => 58,
                'state_id' => 4,
                'city_name' => 'Kamrup',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            58 => 
            array (
                'id' => 59,
                'state_id' => 4,
                'city_name' => 'Kamrup Metropolitan',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            59 => 
            array (
                'id' => 60,
                'state_id' => 4,
                'city_name' => 'Karbi Anglong',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            60 => 
            array (
                'id' => 61,
                'state_id' => 4,
                'city_name' => 'Karimganj',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            61 => 
            array (
                'id' => 62,
                'state_id' => 4,
                'city_name' => 'Kokrajhar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            62 => 
            array (
                'id' => 63,
                'state_id' => 4,
                'city_name' => 'Lakhimpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            63 => 
            array (
                'id' => 64,
                'state_id' => 4,
                'city_name' => 'Majuli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            64 => 
            array (
                'id' => 65,
                'state_id' => 4,
                'city_name' => 'Morigaon',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            65 => 
            array (
                'id' => 66,
                'state_id' => 4,
                'city_name' => 'Nagaon',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            66 => 
            array (
                'id' => 67,
                'state_id' => 4,
                'city_name' => 'Nalbari',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            67 => 
            array (
                'id' => 68,
                'state_id' => 4,
                'city_name' => 'Sivasagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            68 => 
            array (
                'id' => 69,
                'state_id' => 4,
                'city_name' => 'Sonitpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            69 => 
            array (
                'id' => 70,
                'state_id' => 4,
                'city_name' => 'South Salmara-Mankachar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            70 => 
            array (
                'id' => 71,
                'state_id' => 4,
                'city_name' => 'Tinsukia',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            71 => 
            array (
                'id' => 72,
                'state_id' => 4,
                'city_name' => 'Udalguri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            72 => 
            array (
                'id' => 73,
                'state_id' => 4,
                'city_name' => 'West Karbi Anglong',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            73 => 
            array (
                'id' => 74,
                'state_id' => 5,
                'city_name' => 'Araria',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            74 => 
            array (
                'id' => 75,
                'state_id' => 5,
                'city_name' => 'Arwal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            75 => 
            array (
                'id' => 76,
                'state_id' => 5,
                'city_name' => 'Aurangabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            76 => 
            array (
                'id' => 77,
                'state_id' => 5,
                'city_name' => 'Banka',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            77 => 
            array (
                'id' => 78,
                'state_id' => 5,
                'city_name' => 'Begusarai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            78 => 
            array (
                'id' => 79,
                'state_id' => 5,
                'city_name' => 'Bhagalpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            79 => 
            array (
                'id' => 80,
                'state_id' => 5,
                'city_name' => 'Bhojpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            80 => 
            array (
                'id' => 81,
                'state_id' => 5,
                'city_name' => 'Buxar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            81 => 
            array (
                'id' => 82,
                'state_id' => 5,
                'city_name' => 'Darbhanga',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            82 => 
            array (
                'id' => 83,
                'state_id' => 5,
                'city_name' => 'East Champaran',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            83 => 
            array (
                'id' => 84,
                'state_id' => 5,
                'city_name' => 'Gaya',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            84 => 
            array (
                'id' => 85,
                'state_id' => 5,
                'city_name' => 'Gopalganj',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            85 => 
            array (
                'id' => 86,
                'state_id' => 5,
                'city_name' => 'Jamui',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            86 => 
            array (
                'id' => 87,
                'state_id' => 5,
                'city_name' => 'Jehanabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            87 => 
            array (
                'id' => 88,
                'state_id' => 5,
                'city_name' => 'Kaimur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            88 => 
            array (
                'id' => 89,
                'state_id' => 5,
                'city_name' => 'Katihar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            89 => 
            array (
                'id' => 90,
                'state_id' => 5,
                'city_name' => 'Khagaria',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            90 => 
            array (
                'id' => 91,
                'state_id' => 5,
                'city_name' => 'Kishanganj',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            91 => 
            array (
                'id' => 92,
                'state_id' => 5,
                'city_name' => 'Lakhisarai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            92 => 
            array (
                'id' => 93,
                'state_id' => 5,
                'city_name' => 'Madhepura',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            93 => 
            array (
                'id' => 94,
                'state_id' => 5,
                'city_name' => 'Madhubani',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            94 => 
            array (
                'id' => 95,
                'state_id' => 5,
                'city_name' => 'Munger',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            95 => 
            array (
                'id' => 96,
                'state_id' => 5,
                'city_name' => 'Muzaffarpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            96 => 
            array (
                'id' => 97,
                'state_id' => 5,
                'city_name' => 'Nalanda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            97 => 
            array (
                'id' => 98,
                'state_id' => 5,
                'city_name' => 'Nawada',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            98 => 
            array (
                'id' => 99,
                'state_id' => 5,
                'city_name' => 'Patna',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            99 => 
            array (
                'id' => 100,
                'state_id' => 5,
                'city_name' => 'Purnia',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            100 => 
            array (
                'id' => 101,
                'state_id' => 5,
                'city_name' => 'Rohtas',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            101 => 
            array (
                'id' => 102,
                'state_id' => 5,
                'city_name' => 'Saharsa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            102 => 
            array (
                'id' => 103,
                'state_id' => 5,
                'city_name' => 'Samastipur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            103 => 
            array (
                'id' => 104,
                'state_id' => 5,
                'city_name' => 'Saran',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            104 => 
            array (
                'id' => 105,
                'state_id' => 5,
                'city_name' => 'Sheikhpura',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            105 => 
            array (
                'id' => 106,
                'state_id' => 5,
                'city_name' => 'Sheohar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            106 => 
            array (
                'id' => 107,
                'state_id' => 5,
                'city_name' => 'Sitamarhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            107 => 
            array (
                'id' => 108,
                'state_id' => 5,
                'city_name' => 'Siwan',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            108 => 
            array (
                'id' => 109,
                'state_id' => 5,
                'city_name' => 'Supaul',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            109 => 
            array (
                'id' => 110,
                'state_id' => 5,
                'city_name' => 'Vaishali',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            110 => 
            array (
                'id' => 111,
                'state_id' => 5,
                'city_name' => 'West Champaran',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            111 => 
            array (
                'id' => 112,
                'state_id' => 6,
                'city_name' => 'Balod',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            112 => 
            array (
                'id' => 113,
                'state_id' => 6,
                'city_name' => 'Baloda Bazar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            113 => 
            array (
                'id' => 114,
                'state_id' => 6,
                'city_name' => 'Balrampur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            114 => 
            array (
                'id' => 115,
                'state_id' => 6,
                'city_name' => 'Bastar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            115 => 
            array (
                'id' => 116,
                'state_id' => 6,
                'city_name' => 'Bemetara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            116 => 
            array (
                'id' => 117,
                'state_id' => 6,
                'city_name' => 'Bijapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            117 => 
            array (
                'id' => 118,
                'state_id' => 6,
                'city_name' => 'Bilaspur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            118 => 
            array (
                'id' => 119,
                'state_id' => 6,
            'city_name' => 'Dantewada (South Bastar)',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            119 => 
            array (
                'id' => 120,
                'state_id' => 6,
                'city_name' => 'Dhamtari',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            120 => 
            array (
                'id' => 121,
                'state_id' => 6,
                'city_name' => 'Durg',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            121 => 
            array (
                'id' => 122,
                'state_id' => 6,
                'city_name' => 'Gariaband',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            122 => 
            array (
                'id' => 123,
                'state_id' => 6,
                'city_name' => 'Janjgir-Champa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            123 => 
            array (
                'id' => 124,
                'state_id' => 6,
                'city_name' => 'Jashpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            124 => 
            array (
                'id' => 125,
                'state_id' => 6,
            'city_name' => 'Kabirdham (Kawardha)',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            125 => 
            array (
                'id' => 126,
                'state_id' => 6,
            'city_name' => 'Kanker (North Bastar)',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            126 => 
            array (
                'id' => 127,
                'state_id' => 6,
                'city_name' => 'Kondagaon',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            127 => 
            array (
                'id' => 128,
                'state_id' => 6,
                'city_name' => 'Korba',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            128 => 
            array (
                'id' => 129,
                'state_id' => 6,
                'city_name' => 'Koriya',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            129 => 
            array (
                'id' => 130,
                'state_id' => 6,
                'city_name' => 'Mahasamund',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            130 => 
            array (
                'id' => 131,
                'state_id' => 6,
                'city_name' => 'Mungeli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            131 => 
            array (
                'id' => 132,
                'state_id' => 6,
                'city_name' => 'Narayanpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            132 => 
            array (
                'id' => 133,
                'state_id' => 6,
                'city_name' => 'Raigarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            133 => 
            array (
                'id' => 134,
                'state_id' => 6,
                'city_name' => 'Raipur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            134 => 
            array (
                'id' => 135,
                'state_id' => 6,
                'city_name' => 'Rajnandgaon',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            135 => 
            array (
                'id' => 136,
                'state_id' => 6,
                'city_name' => 'Sukma',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            136 => 
            array (
                'id' => 137,
                'state_id' => 6,
                'city_name' => 'Surajpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            137 => 
            array (
                'id' => 138,
                'state_id' => 6,
                'city_name' => 'Surguja',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            138 => 
            array (
                'id' => 139,
                'state_id' => 7,
                'city_name' => 'Chandigarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            139 => 
            array (
                'id' => 140,
                'state_id' => 8,
                'city_name' => 'Daman',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            140 => 
            array (
                'id' => 141,
                'state_id' => 8,
                'city_name' => 'Diu',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            141 => 
            array (
                'id' => 142,
                'state_id' => 9,
                'city_name' => 'Central Delhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            142 => 
            array (
                'id' => 143,
                'state_id' => 9,
                'city_name' => 'East Delhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            143 => 
            array (
                'id' => 144,
                'state_id' => 9,
                'city_name' => 'New Delhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            144 => 
            array (
                'id' => 145,
                'state_id' => 9,
                'city_name' => 'North Delhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            145 => 
            array (
                'id' => 146,
                'state_id' => 9,
                'city_name' => 'North East Delhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            146 => 
            array (
                'id' => 147,
                'state_id' => 9,
                'city_name' => 'North West Delhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            147 => 
            array (
                'id' => 148,
                'state_id' => 9,
                'city_name' => 'Shahdara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            148 => 
            array (
                'id' => 149,
                'state_id' => 9,
                'city_name' => 'South Delhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            149 => 
            array (
                'id' => 150,
                'state_id' => 9,
                'city_name' => 'South East Delhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            150 => 
            array (
                'id' => 151,
                'state_id' => 9,
                'city_name' => 'South West Delhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            151 => 
            array (
                'id' => 152,
                'state_id' => 9,
                'city_name' => 'West Delhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            152 => 
            array (
                'id' => 153,
                'state_id' => 10,
                'city_name' => 'Dadra and Nagar Haveli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            153 => 
            array (
                'id' => 154,
                'state_id' => 11,
                'city_name' => 'North Goa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            154 => 
            array (
                'id' => 155,
                'state_id' => 11,
                'city_name' => 'South Goa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            155 => 
            array (
                'id' => 156,
                'state_id' => 12,
                'city_name' => 'Ahmedabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            156 => 
            array (
                'id' => 157,
                'state_id' => 12,
                'city_name' => 'Amreli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            157 => 
            array (
                'id' => 158,
                'state_id' => 12,
                'city_name' => 'Anand',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            158 => 
            array (
                'id' => 159,
                'state_id' => 12,
                'city_name' => 'Aravalli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            159 => 
            array (
                'id' => 160,
                'state_id' => 12,
                'city_name' => 'Banaskantha',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            160 => 
            array (
                'id' => 161,
                'state_id' => 12,
                'city_name' => 'Bharuch',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            161 => 
            array (
                'id' => 162,
                'state_id' => 12,
                'city_name' => 'Bhavnagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            162 => 
            array (
                'id' => 163,
                'state_id' => 12,
                'city_name' => 'Botad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            163 => 
            array (
                'id' => 164,
                'state_id' => 12,
                'city_name' => 'Chhota Udepur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            164 => 
            array (
                'id' => 165,
                'state_id' => 12,
                'city_name' => 'Dahod',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            165 => 
            array (
                'id' => 166,
                'state_id' => 12,
                'city_name' => 'Dang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            166 => 
            array (
                'id' => 167,
                'state_id' => 12,
                'city_name' => 'Devbhoomi Dwarka',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            167 => 
            array (
                'id' => 168,
                'state_id' => 12,
                'city_name' => 'Gandhinagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            168 => 
            array (
                'id' => 169,
                'state_id' => 12,
                'city_name' => 'Gir Somnath',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            169 => 
            array (
                'id' => 170,
                'state_id' => 12,
                'city_name' => 'Jamnagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            170 => 
            array (
                'id' => 171,
                'state_id' => 12,
                'city_name' => 'Junagadh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            171 => 
            array (
                'id' => 172,
                'state_id' => 12,
                'city_name' => 'Kheda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            172 => 
            array (
                'id' => 173,
                'state_id' => 12,
                'city_name' => 'Kutch',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            173 => 
            array (
                'id' => 174,
                'state_id' => 12,
                'city_name' => 'Mahisagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            174 => 
            array (
                'id' => 175,
                'state_id' => 12,
                'city_name' => 'Mehsana',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            175 => 
            array (
                'id' => 176,
                'state_id' => 12,
                'city_name' => 'Morbi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            176 => 
            array (
                'id' => 177,
                'state_id' => 12,
                'city_name' => 'Narmada',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            177 => 
            array (
                'id' => 178,
                'state_id' => 12,
                'city_name' => 'Navsari',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            178 => 
            array (
                'id' => 179,
                'state_id' => 12,
                'city_name' => 'Panchmahal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            179 => 
            array (
                'id' => 180,
                'state_id' => 12,
                'city_name' => 'Patan',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            180 => 
            array (
                'id' => 181,
                'state_id' => 12,
                'city_name' => 'Porbandar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            181 => 
            array (
                'id' => 182,
                'state_id' => 12,
                'city_name' => 'Rajkot',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            182 => 
            array (
                'id' => 183,
                'state_id' => 12,
                'city_name' => 'Sabarkantha',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            183 => 
            array (
                'id' => 184,
                'state_id' => 12,
                'city_name' => 'Surat',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            184 => 
            array (
                'id' => 185,
                'state_id' => 12,
                'city_name' => 'Surendranagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            185 => 
            array (
                'id' => 186,
                'state_id' => 12,
                'city_name' => 'Tapi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            186 => 
            array (
                'id' => 187,
                'state_id' => 12,
                'city_name' => 'Vadodara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            187 => 
            array (
                'id' => 188,
                'state_id' => 12,
                'city_name' => 'Valsad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            188 => 
            array (
                'id' => 189,
                'state_id' => 13,
                'city_name' => 'Bilaspur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            189 => 
            array (
                'id' => 190,
                'state_id' => 13,
                'city_name' => 'Chamba',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            190 => 
            array (
                'id' => 191,
                'state_id' => 13,
                'city_name' => 'Hamirpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            191 => 
            array (
                'id' => 192,
                'state_id' => 13,
                'city_name' => 'Kangra',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            192 => 
            array (
                'id' => 193,
                'state_id' => 13,
                'city_name' => 'Kinnaur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            193 => 
            array (
                'id' => 194,
                'state_id' => 13,
                'city_name' => 'Kullu',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            194 => 
            array (
                'id' => 195,
                'state_id' => 13,
                'city_name' => 'Lahaul and Spiti',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            195 => 
            array (
                'id' => 196,
                'state_id' => 13,
                'city_name' => 'Mandi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            196 => 
            array (
                'id' => 197,
                'state_id' => 13,
                'city_name' => 'Shimla',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            197 => 
            array (
                'id' => 198,
                'state_id' => 13,
                'city_name' => 'Sirmaur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            198 => 
            array (
                'id' => 199,
                'state_id' => 13,
                'city_name' => 'Solan',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            199 => 
            array (
                'id' => 200,
                'state_id' => 13,
                'city_name' => 'Una',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            200 => 
            array (
                'id' => 201,
                'state_id' => 14,
                'city_name' => 'Ambala',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            201 => 
            array (
                'id' => 202,
                'state_id' => 14,
                'city_name' => 'Bhiwani',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            202 => 
            array (
                'id' => 203,
                'state_id' => 14,
                'city_name' => 'Charkhi Dadri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            203 => 
            array (
                'id' => 204,
                'state_id' => 14,
                'city_name' => 'Faridabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            204 => 
            array (
                'id' => 205,
                'state_id' => 14,
                'city_name' => 'Fatehabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            205 => 
            array (
                'id' => 206,
                'state_id' => 14,
                'city_name' => 'Gurgaon',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            206 => 
            array (
                'id' => 207,
                'state_id' => 14,
                'city_name' => 'Hisar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            207 => 
            array (
                'id' => 208,
                'state_id' => 14,
                'city_name' => 'Jhajjar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            208 => 
            array (
                'id' => 209,
                'state_id' => 14,
                'city_name' => 'Jind',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            209 => 
            array (
                'id' => 210,
                'state_id' => 14,
                'city_name' => 'Kaithal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            210 => 
            array (
                'id' => 211,
                'state_id' => 14,
                'city_name' => 'Karnal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            211 => 
            array (
                'id' => 212,
                'state_id' => 14,
                'city_name' => 'Kurukshetra',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            212 => 
            array (
                'id' => 213,
                'state_id' => 14,
                'city_name' => 'Mahendragarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            213 => 
            array (
                'id' => 214,
                'state_id' => 14,
                'city_name' => 'Mewat',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            214 => 
            array (
                'id' => 215,
                'state_id' => 14,
                'city_name' => 'Palwal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            215 => 
            array (
                'id' => 216,
                'state_id' => 14,
                'city_name' => 'Panchkula',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            216 => 
            array (
                'id' => 217,
                'state_id' => 14,
                'city_name' => 'Panipat',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            217 => 
            array (
                'id' => 218,
                'state_id' => 14,
                'city_name' => 'Rewari',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            218 => 
            array (
                'id' => 219,
                'state_id' => 14,
                'city_name' => 'Rohtak',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            219 => 
            array (
                'id' => 220,
                'state_id' => 14,
                'city_name' => 'Sirsa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            220 => 
            array (
                'id' => 221,
                'state_id' => 14,
                'city_name' => 'Sonipat',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            221 => 
            array (
                'id' => 222,
                'state_id' => 14,
                'city_name' => 'Yamuna Nagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            222 => 
            array (
                'id' => 223,
                'state_id' => 15,
                'city_name' => 'Anantnag',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            223 => 
            array (
                'id' => 224,
                'state_id' => 15,
                'city_name' => 'Bandipore',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            224 => 
            array (
                'id' => 225,
                'state_id' => 15,
                'city_name' => 'Baramulla',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            225 => 
            array (
                'id' => 226,
                'state_id' => 15,
                'city_name' => 'Budgam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            226 => 
            array (
                'id' => 227,
                'state_id' => 15,
                'city_name' => 'Doda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            227 => 
            array (
                'id' => 228,
                'state_id' => 15,
                'city_name' => 'Ganderbal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            228 => 
            array (
                'id' => 229,
                'state_id' => 15,
                'city_name' => 'Jammu',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            229 => 
            array (
                'id' => 230,
                'state_id' => 15,
                'city_name' => 'Kathua',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            230 => 
            array (
                'id' => 231,
                'state_id' => 15,
                'city_name' => 'Kishtwar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            231 => 
            array (
                'id' => 232,
                'state_id' => 15,
                'city_name' => 'Kulgam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            232 => 
            array (
                'id' => 233,
                'state_id' => 15,
                'city_name' => 'Kupwara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            233 => 
            array (
                'id' => 234,
                'state_id' => 15,
                'city_name' => 'Poonch',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            234 => 
            array (
                'id' => 235,
                'state_id' => 15,
                'city_name' => 'Pulwama',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            235 => 
            array (
                'id' => 236,
                'state_id' => 15,
                'city_name' => 'Rajouri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            236 => 
            array (
                'id' => 237,
                'state_id' => 15,
                'city_name' => 'Ramban',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            237 => 
            array (
                'id' => 238,
                'state_id' => 15,
                'city_name' => 'Reasi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            238 => 
            array (
                'id' => 239,
                'state_id' => 15,
                'city_name' => 'Samba',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            239 => 
            array (
                'id' => 240,
                'state_id' => 15,
                'city_name' => 'Shopian',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            240 => 
            array (
                'id' => 241,
                'state_id' => 15,
                'city_name' => 'Srinagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            241 => 
            array (
                'id' => 242,
                'state_id' => 15,
                'city_name' => 'Udhampur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            242 => 
            array (
                'id' => 243,
                'state_id' => 16,
                'city_name' => 'Bokaro',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            243 => 
            array (
                'id' => 244,
                'state_id' => 16,
                'city_name' => 'Chatra',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            244 => 
            array (
                'id' => 245,
                'state_id' => 16,
                'city_name' => 'Deoghar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            245 => 
            array (
                'id' => 246,
                'state_id' => 16,
                'city_name' => 'Dhanbad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            246 => 
            array (
                'id' => 247,
                'state_id' => 16,
                'city_name' => 'Dumka',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            247 => 
            array (
                'id' => 248,
                'state_id' => 16,
                'city_name' => 'East Singhbhum',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            248 => 
            array (
                'id' => 249,
                'state_id' => 16,
                'city_name' => 'Garhwa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            249 => 
            array (
                'id' => 250,
                'state_id' => 16,
                'city_name' => 'Giridih',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            250 => 
            array (
                'id' => 251,
                'state_id' => 16,
                'city_name' => 'Godda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            251 => 
            array (
                'id' => 252,
                'state_id' => 16,
                'city_name' => 'Gumla',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            252 => 
            array (
                'id' => 253,
                'state_id' => 16,
                'city_name' => 'Hazaribagh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            253 => 
            array (
                'id' => 254,
                'state_id' => 16,
                'city_name' => 'Jamtara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            254 => 
            array (
                'id' => 255,
                'state_id' => 16,
                'city_name' => 'Khunti',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            255 => 
            array (
                'id' => 256,
                'state_id' => 16,
                'city_name' => 'Koderma',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            256 => 
            array (
                'id' => 257,
                'state_id' => 16,
                'city_name' => 'Latehar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            257 => 
            array (
                'id' => 258,
                'state_id' => 16,
                'city_name' => 'Lohardaga',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            258 => 
            array (
                'id' => 259,
                'state_id' => 16,
                'city_name' => 'Pakur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            259 => 
            array (
                'id' => 260,
                'state_id' => 16,
                'city_name' => 'Palamu',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            260 => 
            array (
                'id' => 261,
                'state_id' => 16,
                'city_name' => 'Ramgarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            261 => 
            array (
                'id' => 262,
                'state_id' => 16,
                'city_name' => 'Ranchi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            262 => 
            array (
                'id' => 263,
                'state_id' => 16,
                'city_name' => 'Sahebganj',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            263 => 
            array (
                'id' => 264,
                'state_id' => 16,
                'city_name' => 'Seraikela-Kharsawan',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            264 => 
            array (
                'id' => 265,
                'state_id' => 16,
                'city_name' => 'Simdega',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            265 => 
            array (
                'id' => 266,
                'state_id' => 16,
                'city_name' => 'West Singhbhum',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            266 => 
            array (
                'id' => 267,
                'state_id' => 17,
                'city_name' => 'Alappuzha',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            267 => 
            array (
                'id' => 268,
                'state_id' => 17,
                'city_name' => 'Ernakulam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            268 => 
            array (
                'id' => 269,
                'state_id' => 17,
                'city_name' => 'Idukki',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            269 => 
            array (
                'id' => 270,
                'state_id' => 17,
                'city_name' => 'Kannur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            270 => 
            array (
                'id' => 271,
                'state_id' => 17,
                'city_name' => 'Kasaragod',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            271 => 
            array (
                'id' => 272,
                'state_id' => 17,
                'city_name' => 'Kollam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            272 => 
            array (
                'id' => 273,
                'state_id' => 17,
                'city_name' => 'Kottayam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            273 => 
            array (
                'id' => 274,
                'state_id' => 17,
                'city_name' => 'Kozhikode',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            274 => 
            array (
                'id' => 275,
                'state_id' => 17,
                'city_name' => 'Malappuram',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            275 => 
            array (
                'id' => 276,
                'state_id' => 17,
                'city_name' => 'Palakkad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            276 => 
            array (
                'id' => 277,
                'state_id' => 17,
                'city_name' => 'Pathanamthitta',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            277 => 
            array (
                'id' => 278,
                'state_id' => 17,
                'city_name' => 'Thiruvananthapuram',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            278 => 
            array (
                'id' => 279,
                'state_id' => 17,
                'city_name' => 'Thrissur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            279 => 
            array (
                'id' => 280,
                'state_id' => 17,
                'city_name' => 'Wayanad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            280 => 
            array (
                'id' => 281,
                'state_id' => 18,
                'city_name' => 'Bagalkot',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            281 => 
            array (
                'id' => 282,
                'state_id' => 18,
                'city_name' => 'Ballari',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            282 => 
            array (
                'id' => 283,
                'state_id' => 18,
                'city_name' => 'Belagavi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            283 => 
            array (
                'id' => 284,
                'state_id' => 18,
                'city_name' => 'Bengaluru',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            284 => 
            array (
                'id' => 285,
                'state_id' => 18,
                'city_name' => 'Bengaluru Rural',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            285 => 
            array (
                'id' => 286,
                'state_id' => 18,
                'city_name' => 'Bidar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            286 => 
            array (
                'id' => 287,
                'state_id' => 18,
                'city_name' => 'Chamarajanagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            287 => 
            array (
                'id' => 288,
                'state_id' => 18,
                'city_name' => 'Chikkaballapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            288 => 
            array (
                'id' => 289,
                'state_id' => 18,
                'city_name' => 'Chikkamagaluru',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            289 => 
            array (
                'id' => 290,
                'state_id' => 18,
                'city_name' => 'Chitradurga',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            290 => 
            array (
                'id' => 291,
                'state_id' => 18,
                'city_name' => 'Dakshina Kannada',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            291 => 
            array (
                'id' => 292,
                'state_id' => 18,
                'city_name' => 'Davangere',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            292 => 
            array (
                'id' => 293,
                'state_id' => 18,
                'city_name' => 'Dharwad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            293 => 
            array (
                'id' => 294,
                'state_id' => 18,
                'city_name' => 'Gadag',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            294 => 
            array (
                'id' => 295,
                'state_id' => 18,
                'city_name' => 'Hassan',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            295 => 
            array (
                'id' => 296,
                'state_id' => 18,
                'city_name' => 'Haveri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            296 => 
            array (
                'id' => 297,
                'state_id' => 18,
                'city_name' => 'Kalaburagi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            297 => 
            array (
                'id' => 298,
                'state_id' => 18,
                'city_name' => 'Kodagu',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            298 => 
            array (
                'id' => 299,
                'state_id' => 18,
                'city_name' => 'Kolar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            299 => 
            array (
                'id' => 300,
                'state_id' => 18,
                'city_name' => 'Koppal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            300 => 
            array (
                'id' => 301,
                'state_id' => 18,
                'city_name' => 'Mandya',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            301 => 
            array (
                'id' => 302,
                'state_id' => 18,
                'city_name' => 'Mysuru',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            302 => 
            array (
                'id' => 303,
                'state_id' => 18,
                'city_name' => 'Raichur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            303 => 
            array (
                'id' => 304,
                'state_id' => 18,
                'city_name' => 'Ramanagara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            304 => 
            array (
                'id' => 305,
                'state_id' => 18,
                'city_name' => 'Shivamogga',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            305 => 
            array (
                'id' => 306,
                'state_id' => 18,
                'city_name' => 'Tumakuru',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            306 => 
            array (
                'id' => 307,
                'state_id' => 18,
                'city_name' => 'Udupi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            307 => 
            array (
                'id' => 308,
                'state_id' => 18,
                'city_name' => 'Uttara Kannada',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            308 => 
            array (
                'id' => 309,
                'state_id' => 18,
                'city_name' => 'Vijayapura',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            309 => 
            array (
                'id' => 310,
                'state_id' => 18,
                'city_name' => 'Yadgir',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            310 => 
            array (
                'id' => 311,
                'state_id' => 19,
                'city_name' => 'Lakshadweep',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            311 => 
            array (
                'id' => 312,
                'state_id' => 20,
                'city_name' => 'East Garo Hills',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            312 => 
            array (
                'id' => 313,
                'state_id' => 20,
                'city_name' => 'East Khasi Hills',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            313 => 
            array (
                'id' => 314,
                'state_id' => 20,
                'city_name' => 'Jaintia Hills',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            314 => 
            array (
                'id' => 315,
                'state_id' => 20,
                'city_name' => 'Ri-Bhoi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            315 => 
            array (
                'id' => 316,
                'state_id' => 20,
                'city_name' => 'South Garo Hills',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            316 => 
            array (
                'id' => 317,
                'state_id' => 20,
                'city_name' => 'West Garo Hills',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            317 => 
            array (
                'id' => 318,
                'state_id' => 20,
                'city_name' => 'West Khasi Hills',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            318 => 
            array (
                'id' => 319,
                'state_id' => 21,
                'city_name' => 'Ahmednagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            319 => 
            array (
                'id' => 320,
                'state_id' => 21,
                'city_name' => 'Akola',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            320 => 
            array (
                'id' => 321,
                'state_id' => 21,
                'city_name' => 'Amravati',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            321 => 
            array (
                'id' => 322,
                'state_id' => 21,
                'city_name' => 'Aurangabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            322 => 
            array (
                'id' => 323,
                'state_id' => 21,
                'city_name' => 'Beed',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            323 => 
            array (
                'id' => 324,
                'state_id' => 21,
                'city_name' => 'Bhandara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            324 => 
            array (
                'id' => 325,
                'state_id' => 21,
                'city_name' => 'Buldhana',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            325 => 
            array (
                'id' => 326,
                'state_id' => 21,
                'city_name' => 'Chandrapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            326 => 
            array (
                'id' => 327,
                'state_id' => 21,
                'city_name' => 'Dhule',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            327 => 
            array (
                'id' => 328,
                'state_id' => 21,
                'city_name' => 'Gadchiroli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            328 => 
            array (
                'id' => 329,
                'state_id' => 21,
                'city_name' => 'Gondia',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            329 => 
            array (
                'id' => 330,
                'state_id' => 21,
                'city_name' => 'Hingoli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            330 => 
            array (
                'id' => 331,
                'state_id' => 21,
                'city_name' => 'Jalgaon',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            331 => 
            array (
                'id' => 332,
                'state_id' => 21,
                'city_name' => 'Jalna',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            332 => 
            array (
                'id' => 333,
                'state_id' => 21,
                'city_name' => 'Kolhapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            333 => 
            array (
                'id' => 334,
                'state_id' => 21,
                'city_name' => 'Latur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            334 => 
            array (
                'id' => 335,
                'state_id' => 21,
                'city_name' => 'Mumbai City',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            335 => 
            array (
                'id' => 336,
                'state_id' => 21,
                'city_name' => 'Mumbai Suburban',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            336 => 
            array (
                'id' => 337,
                'state_id' => 21,
                'city_name' => 'Nagpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            337 => 
            array (
                'id' => 338,
                'state_id' => 21,
                'city_name' => 'Nanded',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            338 => 
            array (
                'id' => 339,
                'state_id' => 21,
                'city_name' => 'Nandurbar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            339 => 
            array (
                'id' => 340,
                'state_id' => 21,
                'city_name' => 'Nashik',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            340 => 
            array (
                'id' => 341,
                'state_id' => 21,
                'city_name' => 'Osmanabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            341 => 
            array (
                'id' => 342,
                'state_id' => 21,
                'city_name' => 'Palghar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            342 => 
            array (
                'id' => 343,
                'state_id' => 21,
                'city_name' => 'Parbhani',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            343 => 
            array (
                'id' => 344,
                'state_id' => 21,
                'city_name' => 'Pune',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            344 => 
            array (
                'id' => 345,
                'state_id' => 21,
                'city_name' => 'Raigad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            345 => 
            array (
                'id' => 346,
                'state_id' => 21,
                'city_name' => 'Ratnagiri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            346 => 
            array (
                'id' => 347,
                'state_id' => 21,
                'city_name' => 'Sangli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            347 => 
            array (
                'id' => 348,
                'state_id' => 21,
                'city_name' => 'Satara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            348 => 
            array (
                'id' => 349,
                'state_id' => 21,
                'city_name' => 'Sindhudurg',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            349 => 
            array (
                'id' => 350,
                'state_id' => 21,
                'city_name' => 'Solapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            350 => 
            array (
                'id' => 351,
                'state_id' => 21,
                'city_name' => 'Thane',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            351 => 
            array (
                'id' => 352,
                'state_id' => 21,
                'city_name' => 'Wardha',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            352 => 
            array (
                'id' => 353,
                'state_id' => 21,
                'city_name' => 'Washim',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            353 => 
            array (
                'id' => 354,
                'state_id' => 21,
                'city_name' => 'Yavatmal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            354 => 
            array (
                'id' => 355,
                'state_id' => 22,
                'city_name' => 'Bishnupur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            355 => 
            array (
                'id' => 356,
                'state_id' => 22,
                'city_name' => 'Chandel',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            356 => 
            array (
                'id' => 357,
                'state_id' => 22,
                'city_name' => 'Churachandpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            357 => 
            array (
                'id' => 358,
                'state_id' => 22,
                'city_name' => 'Imphal East',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            358 => 
            array (
                'id' => 359,
                'state_id' => 22,
                'city_name' => 'Imphal West',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            359 => 
            array (
                'id' => 360,
                'state_id' => 22,
                'city_name' => 'Jiribam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            360 => 
            array (
                'id' => 361,
                'state_id' => 22,
                'city_name' => 'Kakching',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            361 => 
            array (
                'id' => 362,
                'state_id' => 22,
                'city_name' => 'Kamjong',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            362 => 
            array (
                'id' => 363,
                'state_id' => 22,
                'city_name' => 'Kangpokpi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            363 => 
            array (
                'id' => 364,
                'state_id' => 22,
                'city_name' => 'Noney',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            364 => 
            array (
                'id' => 365,
                'state_id' => 22,
                'city_name' => 'Pherzawl',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            365 => 
            array (
                'id' => 366,
                'state_id' => 22,
                'city_name' => 'Senapati',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            366 => 
            array (
                'id' => 367,
                'state_id' => 22,
                'city_name' => 'Tamenglong',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            367 => 
            array (
                'id' => 368,
                'state_id' => 22,
                'city_name' => 'Tengnoupal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            368 => 
            array (
                'id' => 369,
                'state_id' => 22,
                'city_name' => 'Thoubal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            369 => 
            array (
                'id' => 370,
                'state_id' => 22,
                'city_name' => 'Ukhrul',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            370 => 
            array (
                'id' => 371,
                'state_id' => 23,
                'city_name' => 'Agar Malwa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            371 => 
            array (
                'id' => 372,
                'state_id' => 23,
                'city_name' => 'Alirajpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            372 => 
            array (
                'id' => 373,
                'state_id' => 23,
                'city_name' => 'Anuppur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            373 => 
            array (
                'id' => 374,
                'state_id' => 23,
                'city_name' => 'Ashoknagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            374 => 
            array (
                'id' => 375,
                'state_id' => 23,
                'city_name' => 'Balaghat',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            375 => 
            array (
                'id' => 376,
                'state_id' => 23,
                'city_name' => 'Barwani',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            376 => 
            array (
                'id' => 377,
                'state_id' => 23,
                'city_name' => 'Betul',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            377 => 
            array (
                'id' => 378,
                'state_id' => 23,
                'city_name' => 'Bhind',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            378 => 
            array (
                'id' => 379,
                'state_id' => 23,
                'city_name' => 'Bhopal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            379 => 
            array (
                'id' => 380,
                'state_id' => 23,
                'city_name' => 'Burhanpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            380 => 
            array (
                'id' => 381,
                'state_id' => 23,
                'city_name' => 'Chhatarpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            381 => 
            array (
                'id' => 382,
                'state_id' => 23,
                'city_name' => 'Chhindwara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            382 => 
            array (
                'id' => 383,
                'state_id' => 23,
                'city_name' => 'Damoh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            383 => 
            array (
                'id' => 384,
                'state_id' => 23,
                'city_name' => 'Datia',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            384 => 
            array (
                'id' => 385,
                'state_id' => 23,
                'city_name' => 'Dewas',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            385 => 
            array (
                'id' => 386,
                'state_id' => 23,
                'city_name' => 'Dhar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            386 => 
            array (
                'id' => 387,
                'state_id' => 23,
                'city_name' => 'Dindori',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            387 => 
            array (
                'id' => 388,
                'state_id' => 23,
                'city_name' => 'Guna',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            388 => 
            array (
                'id' => 389,
                'state_id' => 23,
                'city_name' => 'Gwalior',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            389 => 
            array (
                'id' => 390,
                'state_id' => 23,
                'city_name' => 'Harda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            390 => 
            array (
                'id' => 391,
                'state_id' => 23,
                'city_name' => 'Hoshangabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            391 => 
            array (
                'id' => 392,
                'state_id' => 23,
                'city_name' => 'Indore',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            392 => 
            array (
                'id' => 393,
                'state_id' => 23,
                'city_name' => 'Jabalpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            393 => 
            array (
                'id' => 394,
                'state_id' => 23,
                'city_name' => 'Jhabua',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            394 => 
            array (
                'id' => 395,
                'state_id' => 23,
                'city_name' => 'Katni',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            395 => 
            array (
                'id' => 396,
                'state_id' => 23,
                'city_name' => 'Khandwa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            396 => 
            array (
                'id' => 397,
                'state_id' => 23,
                'city_name' => 'Khargone',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            397 => 
            array (
                'id' => 398,
                'state_id' => 23,
                'city_name' => 'Mandla',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            398 => 
            array (
                'id' => 399,
                'state_id' => 23,
                'city_name' => 'Mandsaur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            399 => 
            array (
                'id' => 400,
                'state_id' => 23,
                'city_name' => 'Morena',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            400 => 
            array (
                'id' => 401,
                'state_id' => 23,
                'city_name' => 'Narsinghpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            401 => 
            array (
                'id' => 402,
                'state_id' => 23,
                'city_name' => 'Neemuch',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            402 => 
            array (
                'id' => 403,
                'state_id' => 23,
                'city_name' => 'Niwari',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            403 => 
            array (
                'id' => 404,
                'state_id' => 23,
                'city_name' => 'Panna',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            404 => 
            array (
                'id' => 405,
                'state_id' => 23,
                'city_name' => 'Raisen',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            405 => 
            array (
                'id' => 406,
                'state_id' => 23,
                'city_name' => 'Rajgarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            406 => 
            array (
                'id' => 407,
                'state_id' => 23,
                'city_name' => 'Ratlam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            407 => 
            array (
                'id' => 408,
                'state_id' => 23,
                'city_name' => 'Rewa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            408 => 
            array (
                'id' => 409,
                'state_id' => 23,
                'city_name' => 'Sagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            409 => 
            array (
                'id' => 410,
                'state_id' => 23,
                'city_name' => 'Satna',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            410 => 
            array (
                'id' => 411,
                'state_id' => 23,
                'city_name' => 'Sehore',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            411 => 
            array (
                'id' => 412,
                'state_id' => 23,
                'city_name' => 'Seoni',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            412 => 
            array (
                'id' => 413,
                'state_id' => 23,
                'city_name' => 'Shahdol',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            413 => 
            array (
                'id' => 414,
                'state_id' => 23,
                'city_name' => 'Shajapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            414 => 
            array (
                'id' => 415,
                'state_id' => 23,
                'city_name' => 'Sheopur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            415 => 
            array (
                'id' => 416,
                'state_id' => 23,
                'city_name' => 'Shivpuri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            416 => 
            array (
                'id' => 417,
                'state_id' => 23,
                'city_name' => 'Sidhi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            417 => 
            array (
                'id' => 418,
                'state_id' => 23,
                'city_name' => 'Singrauli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            418 => 
            array (
                'id' => 419,
                'state_id' => 23,
                'city_name' => 'Tikamgarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            419 => 
            array (
                'id' => 420,
                'state_id' => 23,
                'city_name' => 'Ujjain',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            420 => 
            array (
                'id' => 421,
                'state_id' => 23,
                'city_name' => 'Umaria',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            421 => 
            array (
                'id' => 422,
                'state_id' => 23,
                'city_name' => 'Vidisha',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            422 => 
            array (
                'id' => 423,
                'state_id' => 24,
                'city_name' => 'Aizawl',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            423 => 
            array (
                'id' => 424,
                'state_id' => 24,
                'city_name' => 'Champhai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            424 => 
            array (
                'id' => 425,
                'state_id' => 24,
                'city_name' => 'Kolasib',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            425 => 
            array (
                'id' => 426,
                'state_id' => 24,
                'city_name' => 'Lawngtlai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            426 => 
            array (
                'id' => 427,
                'state_id' => 24,
                'city_name' => 'Lunglei',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            427 => 
            array (
                'id' => 428,
                'state_id' => 24,
                'city_name' => 'Mamit',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            428 => 
            array (
                'id' => 429,
                'state_id' => 24,
                'city_name' => 'Saiha',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            429 => 
            array (
                'id' => 430,
                'state_id' => 24,
                'city_name' => 'Serchhip',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            430 => 
            array (
                'id' => 431,
                'state_id' => 25,
                'city_name' => 'Dimapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            431 => 
            array (
                'id' => 432,
                'state_id' => 25,
                'city_name' => 'Kiphire',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            432 => 
            array (
                'id' => 433,
                'state_id' => 25,
                'city_name' => 'Kohima',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            433 => 
            array (
                'id' => 434,
                'state_id' => 25,
                'city_name' => 'Longleng',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            434 => 
            array (
                'id' => 435,
                'state_id' => 25,
                'city_name' => 'Mokokchung',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            435 => 
            array (
                'id' => 436,
                'state_id' => 25,
                'city_name' => 'Mon',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            436 => 
            array (
                'id' => 437,
                'state_id' => 25,
                'city_name' => 'Peren',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            437 => 
            array (
                'id' => 438,
                'state_id' => 25,
                'city_name' => 'Phek',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            438 => 
            array (
                'id' => 439,
                'state_id' => 25,
                'city_name' => 'Tuensang',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            439 => 
            array (
                'id' => 440,
                'state_id' => 25,
                'city_name' => 'Wokha',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            440 => 
            array (
                'id' => 441,
                'state_id' => 25,
                'city_name' => 'Zunheboto',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            441 => 
            array (
                'id' => 442,
                'state_id' => 26,
                'city_name' => 'Angul',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            442 => 
            array (
                'id' => 443,
                'state_id' => 26,
                'city_name' => 'Balangir',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            443 => 
            array (
                'id' => 444,
                'state_id' => 26,
                'city_name' => 'Balasore',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            444 => 
            array (
                'id' => 445,
                'state_id' => 26,
                'city_name' => 'Bargarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            445 => 
            array (
                'id' => 446,
                'state_id' => 26,
                'city_name' => 'Bhadrak',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            446 => 
            array (
                'id' => 447,
                'state_id' => 26,
                'city_name' => 'Boudh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            447 => 
            array (
                'id' => 448,
                'state_id' => 26,
                'city_name' => 'Cuttack',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            448 => 
            array (
                'id' => 449,
                'state_id' => 26,
                'city_name' => 'Debagarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            449 => 
            array (
                'id' => 450,
                'state_id' => 26,
                'city_name' => 'Dhenkanal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            450 => 
            array (
                'id' => 451,
                'state_id' => 26,
                'city_name' => 'Gajapati',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            451 => 
            array (
                'id' => 452,
                'state_id' => 26,
                'city_name' => 'Ganjam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            452 => 
            array (
                'id' => 453,
                'state_id' => 26,
                'city_name' => 'Jagatsinghapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            453 => 
            array (
                'id' => 454,
                'state_id' => 26,
                'city_name' => 'Jajpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            454 => 
            array (
                'id' => 455,
                'state_id' => 26,
                'city_name' => 'Jharsuguda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            455 => 
            array (
                'id' => 456,
                'state_id' => 26,
                'city_name' => 'Kalahandi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            456 => 
            array (
                'id' => 457,
                'state_id' => 26,
                'city_name' => 'Kandhamal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            457 => 
            array (
                'id' => 458,
                'state_id' => 26,
                'city_name' => 'Kendrapara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            458 => 
            array (
                'id' => 459,
                'state_id' => 26,
            'city_name' => 'Kendujhar (Keonjhar)',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            459 => 
            array (
                'id' => 460,
                'state_id' => 26,
                'city_name' => 'Khordha',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            460 => 
            array (
                'id' => 461,
                'state_id' => 26,
                'city_name' => 'Koraput',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            461 => 
            array (
                'id' => 462,
                'state_id' => 26,
                'city_name' => 'Malkangiri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            462 => 
            array (
                'id' => 463,
                'state_id' => 26,
                'city_name' => 'Mayurbhanj',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            463 => 
            array (
                'id' => 464,
                'state_id' => 26,
                'city_name' => 'Nabarangpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            464 => 
            array (
                'id' => 465,
                'state_id' => 26,
                'city_name' => 'Nayagarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            465 => 
            array (
                'id' => 466,
                'state_id' => 26,
                'city_name' => 'Nuapada',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            466 => 
            array (
                'id' => 467,
                'state_id' => 26,
                'city_name' => 'Puri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            467 => 
            array (
                'id' => 468,
                'state_id' => 26,
                'city_name' => 'Rayagada',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            468 => 
            array (
                'id' => 469,
                'state_id' => 26,
                'city_name' => 'Sambalpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            469 => 
            array (
                'id' => 470,
                'state_id' => 26,
            'city_name' => 'Subarnapur (Sonepur)',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            470 => 
            array (
                'id' => 471,
                'state_id' => 26,
                'city_name' => 'Sundargarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            471 => 
            array (
                'id' => 472,
                'state_id' => 27,
                'city_name' => 'Amritsar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            472 => 
            array (
                'id' => 473,
                'state_id' => 27,
                'city_name' => 'Barnala',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            473 => 
            array (
                'id' => 474,
                'state_id' => 27,
                'city_name' => 'Bathinda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            474 => 
            array (
                'id' => 475,
                'state_id' => 27,
                'city_name' => 'Faridkot',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            475 => 
            array (
                'id' => 476,
                'state_id' => 27,
                'city_name' => 'Fatehgarh Sahib',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            476 => 
            array (
                'id' => 477,
                'state_id' => 27,
                'city_name' => 'Fazilka',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            477 => 
            array (
                'id' => 478,
                'state_id' => 27,
                'city_name' => 'Ferozepur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            478 => 
            array (
                'id' => 479,
                'state_id' => 27,
                'city_name' => 'Gurdaspur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            479 => 
            array (
                'id' => 480,
                'state_id' => 27,
                'city_name' => 'Hoshiarpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            480 => 
            array (
                'id' => 481,
                'state_id' => 27,
                'city_name' => 'Jalandhar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            481 => 
            array (
                'id' => 482,
                'state_id' => 27,
                'city_name' => 'Kapurthala',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            482 => 
            array (
                'id' => 483,
                'state_id' => 27,
                'city_name' => 'Ludhiana',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            483 => 
            array (
                'id' => 484,
                'state_id' => 27,
                'city_name' => 'Mansa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            484 => 
            array (
                'id' => 485,
                'state_id' => 27,
                'city_name' => 'Moga',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            485 => 
            array (
                'id' => 486,
                'state_id' => 27,
                'city_name' => 'Muktsar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            486 => 
            array (
                'id' => 487,
                'state_id' => 27,
            'city_name' => 'Nawanshahr (Shahid Bhagat Singh Nagar)',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            487 => 
            array (
                'id' => 488,
                'state_id' => 27,
                'city_name' => 'Pathankot',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            488 => 
            array (
                'id' => 489,
                'state_id' => 27,
                'city_name' => 'Patiala',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            489 => 
            array (
                'id' => 490,
                'state_id' => 27,
                'city_name' => 'Rupnagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            490 => 
            array (
                'id' => 491,
                'state_id' => 27,
            'city_name' => 'Sahibzada Ajit Singh Nagar (Mohali)',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            491 => 
            array (
                'id' => 492,
                'state_id' => 27,
                'city_name' => 'Sangrur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            492 => 
            array (
                'id' => 493,
                'state_id' => 27,
                'city_name' => 'Tarn Taran',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            493 => 
            array (
                'id' => 494,
                'state_id' => 28,
                'city_name' => 'Karaikal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            494 => 
            array (
                'id' => 495,
                'state_id' => 28,
                'city_name' => 'Mahe',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            495 => 
            array (
                'id' => 496,
                'state_id' => 28,
                'city_name' => 'Pondicherry',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            496 => 
            array (
                'id' => 497,
                'state_id' => 28,
                'city_name' => 'Yanam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            497 => 
            array (
                'id' => 498,
                'state_id' => 29,
                'city_name' => 'Ajmer',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            498 => 
            array (
                'id' => 499,
                'state_id' => 29,
                'city_name' => 'Alwar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            499 => 
            array (
                'id' => 500,
                'state_id' => 29,
                'city_name' => 'Banswara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
        ));
        \DB::table('cities')->insert(array (
            0 => 
            array (
                'id' => 501,
                'state_id' => 29,
                'city_name' => 'Baran',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 502,
                'state_id' => 29,
                'city_name' => 'Barmer',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 503,
                'state_id' => 29,
                'city_name' => 'Bharatpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 504,
                'state_id' => 29,
                'city_name' => 'Bhilwara',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 505,
                'state_id' => 29,
                'city_name' => 'Bikaner',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 506,
                'state_id' => 29,
                'city_name' => 'Bundi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 507,
                'state_id' => 29,
                'city_name' => 'Chittorgarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 508,
                'state_id' => 29,
                'city_name' => 'Churu',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 509,
                'state_id' => 29,
                'city_name' => 'Dausa',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 510,
                'state_id' => 29,
                'city_name' => 'Dholpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 511,
                'state_id' => 29,
                'city_name' => 'Dungarpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 512,
                'state_id' => 29,
                'city_name' => 'Hanumangarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 513,
                'state_id' => 29,
                'city_name' => 'Jaipur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 514,
                'state_id' => 29,
                'city_name' => 'Jaisalmer',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            14 => 
            array (
                'id' => 515,
                'state_id' => 29,
                'city_name' => 'Jalore',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            15 => 
            array (
                'id' => 516,
                'state_id' => 29,
                'city_name' => 'Jhalawar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            16 => 
            array (
                'id' => 517,
                'state_id' => 29,
                'city_name' => 'Jhunjhunu',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            17 => 
            array (
                'id' => 518,
                'state_id' => 29,
                'city_name' => 'Jodhpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            18 => 
            array (
                'id' => 519,
                'state_id' => 29,
                'city_name' => 'Karauli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            19 => 
            array (
                'id' => 520,
                'state_id' => 29,
                'city_name' => 'Kota',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            20 => 
            array (
                'id' => 521,
                'state_id' => 29,
                'city_name' => 'Nagaur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            21 => 
            array (
                'id' => 522,
                'state_id' => 29,
                'city_name' => 'Pali',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            22 => 
            array (
                'id' => 523,
                'state_id' => 29,
                'city_name' => 'Pratapgarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            23 => 
            array (
                'id' => 524,
                'state_id' => 29,
                'city_name' => 'Rajsamand',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            24 => 
            array (
                'id' => 525,
                'state_id' => 29,
                'city_name' => 'Sawai Madhopur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            25 => 
            array (
                'id' => 526,
                'state_id' => 29,
                'city_name' => 'Sikar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            26 => 
            array (
                'id' => 527,
                'state_id' => 29,
                'city_name' => 'Sirohi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            27 => 
            array (
                'id' => 528,
                'state_id' => 29,
                'city_name' => 'Sri Ganganagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            28 => 
            array (
                'id' => 529,
                'state_id' => 29,
                'city_name' => 'Tonk',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            29 => 
            array (
                'id' => 530,
                'state_id' => 29,
                'city_name' => 'Udaipur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            30 => 
            array (
                'id' => 531,
                'state_id' => 30,
                'city_name' => 'East Sikkim',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            31 => 
            array (
                'id' => 532,
                'state_id' => 30,
                'city_name' => 'North Sikkim',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            32 => 
            array (
                'id' => 533,
                'state_id' => 30,
                'city_name' => 'South Sikkim',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            33 => 
            array (
                'id' => 534,
                'state_id' => 30,
                'city_name' => 'West Sikkim',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            34 => 
            array (
                'id' => 535,
                'state_id' => 31,
                'city_name' => 'Adilabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            35 => 
            array (
                'id' => 536,
                'state_id' => 31,
                'city_name' => 'Bhadradri Kothagudem',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            36 => 
            array (
                'id' => 537,
                'state_id' => 31,
                'city_name' => 'Hyderabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            37 => 
            array (
                'id' => 538,
                'state_id' => 31,
                'city_name' => 'Jagtial',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            38 => 
            array (
                'id' => 539,
                'state_id' => 31,
                'city_name' => 'Jangaon',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            39 => 
            array (
                'id' => 540,
                'state_id' => 31,
                'city_name' => 'Jayashankar Bhupalapally',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            40 => 
            array (
                'id' => 541,
                'state_id' => 31,
                'city_name' => 'Jogulamba Gadwal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            41 => 
            array (
                'id' => 542,
                'state_id' => 31,
                'city_name' => 'Kamareddy',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            42 => 
            array (
                'id' => 543,
                'state_id' => 31,
                'city_name' => 'Karimnagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            43 => 
            array (
                'id' => 544,
                'state_id' => 31,
                'city_name' => 'Khammam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            44 => 
            array (
                'id' => 545,
                'state_id' => 31,
                'city_name' => 'Komaram Bheem Asifabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            45 => 
            array (
                'id' => 546,
                'state_id' => 31,
                'city_name' => 'Mahabubabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            46 => 
            array (
                'id' => 547,
                'state_id' => 31,
                'city_name' => 'Mahabubnagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            47 => 
            array (
                'id' => 548,
                'state_id' => 31,
                'city_name' => 'Mancherial',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            48 => 
            array (
                'id' => 549,
                'state_id' => 31,
                'city_name' => 'Medak',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            49 => 
            array (
                'id' => 550,
                'state_id' => 31,
                'city_name' => 'Medchal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            50 => 
            array (
                'id' => 551,
                'state_id' => 31,
                'city_name' => 'Mulugu',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            51 => 
            array (
                'id' => 552,
                'state_id' => 31,
                'city_name' => 'Nagarkurnool',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            52 => 
            array (
                'id' => 553,
                'state_id' => 31,
                'city_name' => 'Nalgonda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            53 => 
            array (
                'id' => 554,
                'state_id' => 31,
                'city_name' => 'Narayanpet',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            54 => 
            array (
                'id' => 555,
                'state_id' => 31,
                'city_name' => 'Nirmal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            55 => 
            array (
                'id' => 556,
                'state_id' => 31,
                'city_name' => 'Nizamabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            56 => 
            array (
                'id' => 557,
                'state_id' => 31,
                'city_name' => 'Peddapalli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            57 => 
            array (
                'id' => 558,
                'state_id' => 31,
                'city_name' => 'Rajanna Sircilla',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            58 => 
            array (
                'id' => 559,
                'state_id' => 31,
                'city_name' => 'Ranga Reddy',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            59 => 
            array (
                'id' => 560,
                'state_id' => 31,
                'city_name' => 'Sangareddy',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            60 => 
            array (
                'id' => 561,
                'state_id' => 31,
                'city_name' => 'Siddipet',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            61 => 
            array (
                'id' => 562,
                'state_id' => 31,
                'city_name' => 'Suryapet',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            62 => 
            array (
                'id' => 563,
                'state_id' => 31,
                'city_name' => 'Vikarabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            63 => 
            array (
                'id' => 564,
                'state_id' => 31,
                'city_name' => 'Wanaparthy',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            64 => 
            array (
                'id' => 565,
                'state_id' => 31,
                'city_name' => 'Warangal Rural',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            65 => 
            array (
                'id' => 566,
                'state_id' => 31,
                'city_name' => 'Warangal Urban',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            66 => 
            array (
                'id' => 567,
                'state_id' => 31,
                'city_name' => 'Yadadri Bhuvanagiri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            67 => 
            array (
                'id' => 568,
                'state_id' => 32,
                'city_name' => 'Ariyalur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            68 => 
            array (
                'id' => 569,
                'state_id' => 32,
                'city_name' => 'Chengalpattu',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            69 => 
            array (
                'id' => 570,
                'state_id' => 32,
                'city_name' => 'Chennai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            70 => 
            array (
                'id' => 571,
                'state_id' => 32,
                'city_name' => 'Coimbatore',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            71 => 
            array (
                'id' => 572,
                'state_id' => 32,
                'city_name' => 'Cuddalore',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            72 => 
            array (
                'id' => 573,
                'state_id' => 32,
                'city_name' => 'Dharmapuri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            73 => 
            array (
                'id' => 574,
                'state_id' => 32,
                'city_name' => 'Dindigul',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            74 => 
            array (
                'id' => 575,
                'state_id' => 32,
                'city_name' => 'Erode',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            75 => 
            array (
                'id' => 576,
                'state_id' => 32,
                'city_name' => 'Kallakurichi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            76 => 
            array (
                'id' => 577,
                'state_id' => 32,
                'city_name' => 'Kancheepuram',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            77 => 
            array (
                'id' => 578,
                'state_id' => 32,
                'city_name' => 'Kanyakumari',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            78 => 
            array (
                'id' => 579,
                'state_id' => 32,
                'city_name' => 'Karur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            79 => 
            array (
                'id' => 580,
                'state_id' => 32,
                'city_name' => 'Krishnagiri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            80 => 
            array (
                'id' => 581,
                'state_id' => 32,
                'city_name' => 'Madurai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            81 => 
            array (
                'id' => 582,
                'state_id' => 32,
                'city_name' => 'Nagapattinam',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            82 => 
            array (
                'id' => 583,
                'state_id' => 32,
                'city_name' => 'Namakkal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            83 => 
            array (
                'id' => 584,
                'state_id' => 32,
                'city_name' => 'Nilgiris',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            84 => 
            array (
                'id' => 585,
                'state_id' => 32,
                'city_name' => 'Perambalur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            85 => 
            array (
                'id' => 586,
                'state_id' => 32,
                'city_name' => 'Pudukkottai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            86 => 
            array (
                'id' => 587,
                'state_id' => 32,
                'city_name' => 'Ramanathapuram',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            87 => 
            array (
                'id' => 588,
                'state_id' => 32,
                'city_name' => 'Ranipet',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            88 => 
            array (
                'id' => 589,
                'state_id' => 32,
                'city_name' => 'Salem',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            89 => 
            array (
                'id' => 590,
                'state_id' => 32,
                'city_name' => 'Sivaganga',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            90 => 
            array (
                'id' => 591,
                'state_id' => 32,
                'city_name' => 'Tenkasi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            91 => 
            array (
                'id' => 592,
                'state_id' => 32,
                'city_name' => 'Thanjavur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            92 => 
            array (
                'id' => 593,
                'state_id' => 32,
                'city_name' => 'Theni',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            93 => 
            array (
                'id' => 594,
                'state_id' => 32,
            'city_name' => 'Thoothukudi (Tuticorin)',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            94 => 
            array (
                'id' => 595,
                'state_id' => 32,
                'city_name' => 'Tiruchirappalli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            95 => 
            array (
                'id' => 596,
                'state_id' => 32,
                'city_name' => 'Tirunelveli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            96 => 
            array (
                'id' => 597,
                'state_id' => 32,
                'city_name' => 'Tirupathur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            97 => 
            array (
                'id' => 598,
                'state_id' => 32,
                'city_name' => 'Tiruppur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            98 => 
            array (
                'id' => 599,
                'state_id' => 32,
                'city_name' => 'Tiruvallur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            99 => 
            array (
                'id' => 600,
                'state_id' => 32,
                'city_name' => 'Tiruvannamalai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            100 => 
            array (
                'id' => 601,
                'state_id' => 32,
                'city_name' => 'Tiruvarur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            101 => 
            array (
                'id' => 602,
                'state_id' => 32,
                'city_name' => 'Vellore',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            102 => 
            array (
                'id' => 603,
                'state_id' => 32,
                'city_name' => 'Viluppuram',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            103 => 
            array (
                'id' => 604,
                'state_id' => 32,
                'city_name' => 'Virudhunagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            104 => 
            array (
                'id' => 605,
                'state_id' => 33,
                'city_name' => 'Dhalai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            105 => 
            array (
                'id' => 606,
                'state_id' => 33,
                'city_name' => 'Gomati',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            106 => 
            array (
                'id' => 607,
                'state_id' => 33,
                'city_name' => 'Khowai',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            107 => 
            array (
                'id' => 608,
                'state_id' => 33,
                'city_name' => 'North Tripura',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            108 => 
            array (
                'id' => 609,
                'state_id' => 33,
                'city_name' => 'Sepahijala',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            109 => 
            array (
                'id' => 610,
                'state_id' => 33,
                'city_name' => 'South Tripura',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            110 => 
            array (
                'id' => 611,
                'state_id' => 33,
                'city_name' => 'Unakoti',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            111 => 
            array (
                'id' => 612,
                'state_id' => 33,
                'city_name' => 'West Tripura',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            112 => 
            array (
                'id' => 613,
                'state_id' => 34,
                'city_name' => 'Agra',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            113 => 
            array (
                'id' => 614,
                'state_id' => 34,
                'city_name' => 'Aligarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            114 => 
            array (
                'id' => 615,
                'state_id' => 34,
                'city_name' => 'Ambedkar Nagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            115 => 
            array (
                'id' => 616,
                'state_id' => 34,
                'city_name' => 'Amethi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            116 => 
            array (
                'id' => 617,
                'state_id' => 34,
                'city_name' => 'Amroha',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            117 => 
            array (
                'id' => 618,
                'state_id' => 34,
                'city_name' => 'Auraiya',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            118 => 
            array (
                'id' => 619,
                'state_id' => 34,
                'city_name' => 'Ayodhya',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            119 => 
            array (
                'id' => 620,
                'state_id' => 34,
                'city_name' => 'Azamgarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            120 => 
            array (
                'id' => 621,
                'state_id' => 34,
                'city_name' => 'Baghpat',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            121 => 
            array (
                'id' => 622,
                'state_id' => 34,
                'city_name' => 'Bahraich',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            122 => 
            array (
                'id' => 623,
                'state_id' => 34,
                'city_name' => 'Ballia',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            123 => 
            array (
                'id' => 624,
                'state_id' => 34,
                'city_name' => 'Balrampur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            124 => 
            array (
                'id' => 625,
                'state_id' => 34,
                'city_name' => 'Banda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            125 => 
            array (
                'id' => 626,
                'state_id' => 34,
                'city_name' => 'Barabanki',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            126 => 
            array (
                'id' => 627,
                'state_id' => 34,
                'city_name' => 'Bareilly',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            127 => 
            array (
                'id' => 628,
                'state_id' => 34,
                'city_name' => 'Basti',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            128 => 
            array (
                'id' => 629,
                'state_id' => 34,
                'city_name' => 'Bhadohi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            129 => 
            array (
                'id' => 630,
                'state_id' => 34,
                'city_name' => 'Bijnor',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            130 => 
            array (
                'id' => 631,
                'state_id' => 34,
                'city_name' => 'Budaun',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            131 => 
            array (
                'id' => 632,
                'state_id' => 34,
                'city_name' => 'Bulandshahr',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            132 => 
            array (
                'id' => 633,
                'state_id' => 34,
                'city_name' => 'Chandauli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            133 => 
            array (
                'id' => 634,
                'state_id' => 34,
                'city_name' => 'Chitrakoot',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            134 => 
            array (
                'id' => 635,
                'state_id' => 34,
                'city_name' => 'Deoria',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            135 => 
            array (
                'id' => 636,
                'state_id' => 34,
                'city_name' => 'Etah',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            136 => 
            array (
                'id' => 637,
                'state_id' => 34,
                'city_name' => 'Etawah',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            137 => 
            array (
                'id' => 638,
                'state_id' => 34,
                'city_name' => 'Farrukhabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            138 => 
            array (
                'id' => 639,
                'state_id' => 34,
                'city_name' => 'Fatehpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            139 => 
            array (
                'id' => 640,
                'state_id' => 34,
                'city_name' => 'Firozabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            140 => 
            array (
                'id' => 641,
                'state_id' => 34,
                'city_name' => 'Gautam Buddha Nagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            141 => 
            array (
                'id' => 642,
                'state_id' => 34,
                'city_name' => 'Ghaziabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            142 => 
            array (
                'id' => 643,
                'state_id' => 34,
                'city_name' => 'Ghazipur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            143 => 
            array (
                'id' => 644,
                'state_id' => 34,
                'city_name' => 'Gonda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            144 => 
            array (
                'id' => 645,
                'state_id' => 34,
                'city_name' => 'Gorakhpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            145 => 
            array (
                'id' => 646,
                'state_id' => 34,
                'city_name' => 'Hamirpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            146 => 
            array (
                'id' => 647,
                'state_id' => 34,
                'city_name' => 'Hapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            147 => 
            array (
                'id' => 648,
                'state_id' => 34,
                'city_name' => 'Hardoi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            148 => 
            array (
                'id' => 649,
                'state_id' => 34,
                'city_name' => 'Hathras',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            149 => 
            array (
                'id' => 650,
                'state_id' => 34,
                'city_name' => 'Jalaun',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            150 => 
            array (
                'id' => 651,
                'state_id' => 34,
                'city_name' => 'Jaunpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            151 => 
            array (
                'id' => 652,
                'state_id' => 34,
                'city_name' => 'Jhansi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            152 => 
            array (
                'id' => 653,
                'state_id' => 34,
                'city_name' => 'Kannauj',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            153 => 
            array (
                'id' => 654,
                'state_id' => 34,
                'city_name' => 'Kanpur Dehat',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            154 => 
            array (
                'id' => 655,
                'state_id' => 34,
                'city_name' => 'Kanpur Nagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            155 => 
            array (
                'id' => 656,
                'state_id' => 34,
                'city_name' => 'Kasganj',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            156 => 
            array (
                'id' => 657,
                'state_id' => 34,
                'city_name' => 'Kaushambi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            157 => 
            array (
                'id' => 658,
                'state_id' => 34,
                'city_name' => 'Kheri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            158 => 
            array (
                'id' => 659,
                'state_id' => 34,
                'city_name' => 'Kushinagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            159 => 
            array (
                'id' => 660,
                'state_id' => 34,
                'city_name' => 'Lalitpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            160 => 
            array (
                'id' => 661,
                'state_id' => 34,
                'city_name' => 'Lucknow',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            161 => 
            array (
                'id' => 662,
                'state_id' => 34,
                'city_name' => 'Maharajganj',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            162 => 
            array (
                'id' => 663,
                'state_id' => 34,
                'city_name' => 'Mahoba',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            163 => 
            array (
                'id' => 664,
                'state_id' => 34,
                'city_name' => 'Mainpuri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            164 => 
            array (
                'id' => 665,
                'state_id' => 34,
                'city_name' => 'Mathura',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            165 => 
            array (
                'id' => 666,
                'state_id' => 34,
                'city_name' => 'Mau',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            166 => 
            array (
                'id' => 667,
                'state_id' => 34,
                'city_name' => 'Meerut',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            167 => 
            array (
                'id' => 668,
                'state_id' => 34,
                'city_name' => 'Mirzapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            168 => 
            array (
                'id' => 669,
                'state_id' => 34,
                'city_name' => 'Moradabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            169 => 
            array (
                'id' => 670,
                'state_id' => 34,
                'city_name' => 'Muzaffarnagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            170 => 
            array (
                'id' => 671,
                'state_id' => 34,
                'city_name' => 'Pilibhit',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            171 => 
            array (
                'id' => 672,
                'state_id' => 34,
                'city_name' => 'Pratapgarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            172 => 
            array (
                'id' => 673,
                'state_id' => 34,
                'city_name' => 'RaeBareli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            173 => 
            array (
                'id' => 674,
                'state_id' => 34,
                'city_name' => 'Rampur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            174 => 
            array (
                'id' => 675,
                'state_id' => 34,
                'city_name' => 'Saharanpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            175 => 
            array (
                'id' => 676,
                'state_id' => 34,
                'city_name' => 'Sambhal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            176 => 
            array (
                'id' => 677,
                'state_id' => 34,
                'city_name' => 'Sant Kabir Nagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            177 => 
            array (
                'id' => 678,
                'state_id' => 34,
                'city_name' => 'Shahjahanpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            178 => 
            array (
                'id' => 679,
                'state_id' => 34,
                'city_name' => 'Shamli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            179 => 
            array (
                'id' => 680,
                'state_id' => 34,
                'city_name' => 'Shrawasti',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            180 => 
            array (
                'id' => 681,
                'state_id' => 34,
                'city_name' => 'Siddharthnagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            181 => 
            array (
                'id' => 682,
                'state_id' => 34,
                'city_name' => 'Sitapur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            182 => 
            array (
                'id' => 683,
                'state_id' => 34,
                'city_name' => 'Sonbhadra',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            183 => 
            array (
                'id' => 684,
                'state_id' => 34,
                'city_name' => 'Sultanpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            184 => 
            array (
                'id' => 685,
                'state_id' => 34,
                'city_name' => 'Unnao',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            185 => 
            array (
                'id' => 686,
                'state_id' => 34,
                'city_name' => 'Varanasi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            186 => 
            array (
                'id' => 687,
                'state_id' => 35,
                'city_name' => 'Almora',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            187 => 
            array (
                'id' => 688,
                'state_id' => 35,
                'city_name' => 'Bageshwar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            188 => 
            array (
                'id' => 689,
                'state_id' => 35,
                'city_name' => 'Chamoli',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            189 => 
            array (
                'id' => 690,
                'state_id' => 35,
                'city_name' => 'Champawat',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            190 => 
            array (
                'id' => 691,
                'state_id' => 35,
                'city_name' => 'Dehradun',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            191 => 
            array (
                'id' => 692,
                'state_id' => 35,
                'city_name' => 'Haridwar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            192 => 
            array (
                'id' => 693,
                'state_id' => 35,
                'city_name' => 'Nainital',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            193 => 
            array (
                'id' => 694,
                'state_id' => 35,
                'city_name' => 'Pauri Garhwal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            194 => 
            array (
                'id' => 695,
                'state_id' => 35,
                'city_name' => 'Pithoragarh',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            195 => 
            array (
                'id' => 696,
                'state_id' => 35,
                'city_name' => 'Rudraprayag',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            196 => 
            array (
                'id' => 697,
                'state_id' => 35,
                'city_name' => 'Tehri Garhwal',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            197 => 
            array (
                'id' => 698,
                'state_id' => 35,
                'city_name' => 'Udham Singh Nagar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            198 => 
            array (
                'id' => 699,
                'state_id' => 35,
                'city_name' => 'Uttarkashi',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            199 => 
            array (
                'id' => 700,
                'state_id' => 36,
                'city_name' => 'Alipurduar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            200 => 
            array (
                'id' => 701,
                'state_id' => 36,
                'city_name' => 'Bankura',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            201 => 
            array (
                'id' => 702,
                'state_id' => 36,
                'city_name' => 'Birbhum',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            202 => 
            array (
                'id' => 703,
                'state_id' => 36,
                'city_name' => 'Cooch Behar',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            203 => 
            array (
                'id' => 704,
                'state_id' => 36,
                'city_name' => 'Dakshin Dinajpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            204 => 
            array (
                'id' => 705,
                'state_id' => 36,
                'city_name' => 'Darjeeling',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            205 => 
            array (
                'id' => 706,
                'state_id' => 36,
                'city_name' => 'Hooghly',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            206 => 
            array (
                'id' => 707,
                'state_id' => 36,
                'city_name' => 'Howrah',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            207 => 
            array (
                'id' => 708,
                'state_id' => 36,
                'city_name' => 'Jalpaiguri',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            208 => 
            array (
                'id' => 709,
                'state_id' => 36,
                'city_name' => 'Jhargram',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            209 => 
            array (
                'id' => 710,
                'state_id' => 36,
                'city_name' => 'Kalimpong',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            210 => 
            array (
                'id' => 711,
                'state_id' => 36,
                'city_name' => 'Kolkata',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            211 => 
            array (
                'id' => 712,
                'state_id' => 36,
                'city_name' => 'Malda',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            212 => 
            array (
                'id' => 713,
                'state_id' => 36,
                'city_name' => 'Murshidabad',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            213 => 
            array (
                'id' => 714,
                'state_id' => 36,
                'city_name' => 'Nadia',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            214 => 
            array (
                'id' => 715,
                'state_id' => 36,
                'city_name' => 'North 24 Parganas',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            215 => 
            array (
                'id' => 716,
                'state_id' => 36,
                'city_name' => 'Paschim Bardhaman',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            216 => 
            array (
                'id' => 717,
                'state_id' => 36,
                'city_name' => 'Paschim Medinipur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            217 => 
            array (
                'id' => 718,
                'state_id' => 36,
                'city_name' => 'Purba Bardhaman',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            218 => 
            array (
                'id' => 719,
                'state_id' => 36,
                'city_name' => 'Purba Medinipur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            219 => 
            array (
                'id' => 720,
                'state_id' => 36,
                'city_name' => 'Purulia',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            220 => 
            array (
                'id' => 721,
                'state_id' => 36,
                'city_name' => 'South 24 Parganas',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => NULL,
            ),
            221 => 
            array (
                'id' => 722,
                'state_id' => 36,
                'city_name' => 'Uttar Dinajpur',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2024-08-23 11:11:51',
                'updated_at' => '2024-09-12 11:11:41',
            ),
        ));
        
        
    }
}