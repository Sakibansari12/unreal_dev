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
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('parent_user_id')->nullable()->index('idx_admins_parent_user_id');
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('mobile_no', 30)->nullable();
            $table->string('role', 30)->nullable();
            $table->integer('role_id')->nullable()->index('idx_admins_role_id');
            $table->boolean('status')->default(true);
            $table->text('password');
            $table->string('contact_person')->nullable();
            $table->text('address')->nullable();
            $table->integer('state_id')->nullable();
            $table->string('state_name', 50)->nullable();
            $table->integer('city_id')->nullable();
            $table->string('city_name', 50)->nullable();
            $table->string('gst')->nullable();
            $table->text('sale_plan')->nullable();
            $table->longText('note')->nullable();
            $table->string('discount', 50)->nullable();
            $table->text('remember_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
