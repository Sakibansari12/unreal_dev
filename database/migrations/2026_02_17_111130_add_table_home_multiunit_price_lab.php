<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_home_multi_units', function (Blueprint $table) {
            $table->dateTime('price_lab_sync_date_time')
                  ->nullable()
                  ->after('per_pet_charge'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('tbl_home_multi_units', function (Blueprint $table) {
            $table->dropColumn('price_lab_sync_date_time');
        });
    }
};
