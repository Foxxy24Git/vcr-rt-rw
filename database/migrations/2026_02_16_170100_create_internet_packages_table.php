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
        Schema::create('internet_packages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 18, 2);
            $table->unsignedInteger('validity_value');
            $table->enum('validity_unit', ['hour', 'day', 'month']);
            $table->unsignedInteger('bandwidth_up_kbps')->nullable();
            $table->unsignedInteger('bandwidth_down_kbps')->nullable();
            $table->unsignedBigInteger('quota_mb')->nullable();
            $table->string('mikrotik_profile')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internet_packages');
    }
};
