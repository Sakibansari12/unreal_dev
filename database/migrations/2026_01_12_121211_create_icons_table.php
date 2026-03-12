<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_icons', function (Blueprint $table) {
            $table->id();

            $table->string('icons_name', 255)->nullable();
            $table->text('icons_image')->nullable();
            $table->integer('status')->default(1);
            $table->longText('icons_code')->nullable();
            $table->string('icon_slug', 255)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->string('add_ip', 255)->nullable();
            $table->string('add_by', 255)->nullable();
            $table->string('update_ip', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_icons');
    }
};
