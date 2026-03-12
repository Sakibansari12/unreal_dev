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
        Schema::create('tbl_owner_expenses', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->default(0);
            $table->integer('parent_user_id')->default(0);
            $table->integer('owner_id')->default(0);
            $table->string('expenses_name')->nullable();
            $table->integer('status')->default(0);
            $table->string('file')->nullable();
            $table->integer('property_id')->default(0);
            $table->integer('home_id')->default(0);
            $table->string('pType')->nullable();
            $table->string('date')->nullable();
            $table->string('amount');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_owner_expenses');
    }
};
