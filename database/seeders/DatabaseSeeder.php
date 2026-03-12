<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(TblStatesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(TblHomeTypesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(TblGstsTableSeeder::class);
        $this->call(TblRuAmenitiesTableSeeder::class);
        $this->call(TblWebsiteAmenitiesTableSeeder::class);
        $this->call(TblRuSpecificRoomsTableSeeder::class);
        $this->call(TblRuSpecificRoomAmmenitiesTableSeeder::class);
        $this->call(TblSitesettingsTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(TblGuestSeeder::class);
        $this->call(TblRuFloorTableSeeder::class);
        $this->call(TblRuLocationTableSeeder::class);
    }
}
