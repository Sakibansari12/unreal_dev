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
        Schema::create('sub_sidebars', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('sidebar_id');
            $table->string('name');
            $table->string('slug');
            $table->integer('is_checked')->default(1);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('sub_sidebars');
    }
};
