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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit']);
            $table->enum('source', ['topup', 'voucher_purchase', 'manual_adjustment', 'refund']);
            $table->decimal('amount', 18, 2);
            $table->decimal('balance_before', 18, 2);
            $table->decimal('balance_after', 18, 2);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['wallet_id', 'source', 'created_at'], 'wallet_tx_wallet_source_created_index');
            $table->index(['reference_type', 'reference_id'], 'wallet_tx_reference_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
