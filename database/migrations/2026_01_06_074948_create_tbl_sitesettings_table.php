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
        Schema::create('tbl_sitesettings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name', 250);
            $table->integer('company_mobile_no')->nullable();
            $table->string('company_email', 100)->nullable();
            $table->string('company_prefix')->nullable();
            $table->string('domain_name', 500);
            $table->string('smtp', 200);
            $table->string('auth_email', 200);
            $table->string('auth_email_username', 200);
            $table->string('auth_email_password', 200);
            $table->string('info_email', 200)->nullable();
            $table->string('cc_email', 200)->nullable();
            $table->string('bcc_email', 200)->nullable();
            $table->string('url_rewrite', 10)->default('1');
            $table->string('default_meta_title', 100)->nullable();
            $table->string('default_meta_keyword', 350)->nullable();
            $table->string('default_meta_description', 200)->nullable();
            $table->tinyInteger('is_allow_gst')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->double('website_markup')->default(0);
            $table->string('ru_hash', 500)->nullable();
            $table->integer('blocking_hour')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('booking_manager_name', 100)->nullable();
            $table->string('booking_manager_email', 100)->nullable();
            $table->string('vacation_manager_name')->nullable();
            $table->string('vacation_manager_email')->nullable();
            $table->string('vacation_manager_mobile_no')->nullable();
            $table->string('account_user_name', 100)->nullable();
            $table->string('account_user_email', 100)->nullable();
            $table->string('site_team_name', 100)->nullable();
            $table->string('site_team_email', 100)->nullable();
            $table->string('home_since', 100)->nullable();
            $table->string('home_staff', 100)->nullable();
            $table->string('hosted_families', 100)->nullable();
            $table->string('account_no', 500)->nullable();
            $table->string('PaymentMethodID', 500)->nullable();
            $table->string('LicenceNumber', 500)->nullable();
            $table->integer('currency_in_inr')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sitesettings');
    }
};
