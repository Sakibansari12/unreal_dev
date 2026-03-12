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
        Schema::table('tbl_sitesettings', function (Blueprint $table) {
            $table->tinyInteger('is_pricelab_enabled')->default(0)->after('currency_in_inr');
            $table->string('pricelabs_token')->nullable()->after('is_pricelab_enabled');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_sitesettings', function (Blueprint $table) {
            $table->dropColumn('is_pricelab_enabled');
            $table->dropColumn('pricelabs_token');
        });
    }
};
