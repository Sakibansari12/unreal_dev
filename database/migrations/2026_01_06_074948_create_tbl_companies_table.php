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
        Schema::create('tbl_companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name', 500);
            $table->string('company_address', 1000);
            $table->integer('state_id');
            $table->string('state_name', 100);
            $table->integer('state_code');
            $table->string('gst_no', 20)->nullable();
            $table->string('cin_no', 30)->nullable();
            $table->string('company_phone', 500)->nullable();
            $table->string('company_email', 500)->nullable();
            $table->string('company_website', 500)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('add_ip', 30)->nullable();
            $table->string('add_by', 100)->nullable();
            $table->string('update_ip', 30)->nullable();
            $table->string('update_by', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_companies');
    }
};
