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
        Schema::create('display_keys', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->comment('Label/deskripsi untuk key ini, misal: Bilik 1');
            $table->string('key')->unique();
            $table->unsignedBigInteger('successful_votes')->default(0);
            $table->unsignedBigInteger('failed_attempts')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('display_keys');
    }
};
