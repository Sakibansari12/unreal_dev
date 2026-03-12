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
        Schema::create('user_sidebar_accesses', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('role_id');
            $table->string('sidebar_id', 50);
            $table->integer('sub_sidebar_id')->nullable();
            $table->tinyInteger('is_checked')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->enum('type', ['sidebar', 'subsidebar'])->nullable();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sidebar_accesses');
    }
};
