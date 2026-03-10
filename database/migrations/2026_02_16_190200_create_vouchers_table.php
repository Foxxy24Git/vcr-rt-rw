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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('voucher_batches')->cascadeOnDelete();
            $table->foreignId('reseller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('internet_packages')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->enum('status', ['ready', 'sold', 'used', 'expired', 'revoked']);
            $table->decimal('cost_price', 18, 2);
            $table->decimal('sold_price', 18, 2)->nullable();
            $table->timestamp('generated_at');
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['reseller_id', 'status'], 'vouchers_reseller_status_index');
            $table->index(['batch_id'], 'vouchers_batch_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
