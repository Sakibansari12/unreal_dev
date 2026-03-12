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
        Schema::table('users', function (Blueprint $table) {
            $table->string("last_name", 255)->nullable()->after('name');
            $table->string("country_code",10)->nullable()->before('mobile_no');
            $table->string("state", 55)->nullable()->after('mobile_no');
            $table->string("city", 55)->nullable()->after('state');
            $table->string("zipcode", 55)->nullable()->after('city');
            $table->string("address", 255)->nullable()->after('zipcode');
            $table->string("address_2", 255)->nullable()->after('address');
            $table->string("country_name", 255)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
