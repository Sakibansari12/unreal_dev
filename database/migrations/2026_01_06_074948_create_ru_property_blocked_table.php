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
        Schema::create('ru_property_blocked', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('ru_property_id');
            $table->integer('property_id');
            $table->date('date_from');
            $table->enum('is_available', ['yes', 'no'])->nullable();
            $table->date('date_to');
            $table->string('type',50);
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->string('reason')->nullable();

            $table->index(['date_from', 'type'], 'idx_blocked_dates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ru_property_blocked');
    }
};
