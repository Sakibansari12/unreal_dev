<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TblGuestSeeder extends Seeder
{
    public function run()
    {
        // Table clean karo
        DB::table('tb_guests')->truncate();

        DB::table('tb_guests')->insert([
            [
                'id' => 1,
                'name' => 'Ages 18+',
                'title' => 'Adults',
                'sub_title' => 'Ages 4+',
                'count' => '1',
                'allow_guest_count' => 1,
                'type' => 'Adults',
            ],
            [
                'id' => 2,
                'name' => 'Ages 6-17',
                'title' => 'Children',
                'sub_title' => 'Up to 4 Yrs',
                'count' => '0',
                'allow_guest_count' => 1,
                'type' => 'Children',
            ],
            /* [
                'id' => 3,
                'name' => 'No need to add service animals',
                'title' => 'Pets',
                'sub_title' => 'No need to add service animals',
                'count' => '0',
                'allow_guest_count' => 0,
                'type' => 'Pets',
            ], */
        ]);
    }
}
