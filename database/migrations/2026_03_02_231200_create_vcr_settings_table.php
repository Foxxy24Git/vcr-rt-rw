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
        Schema::create('vcr_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('username_format')->default('{CODE}');
            $table->string('password_format')->default('{RANDOM}');
            $table->unsignedInteger('length')->default(10);
            $table->boolean('allow_numbers')->default(true);
            $table->boolean('allow_uppercase')->default(true);
            $table->boolean('allow_lowercase')->default(false);
            $table->boolean('user_equals_password')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vcr_settings');
    }
};
