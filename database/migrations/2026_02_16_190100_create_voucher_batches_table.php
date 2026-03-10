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
        Schema::create('voucher_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('internet_packages')->cascadeOnDelete();
            $table->string('batch_code')->unique();
            $table->unsignedInteger('qty_requested');
            $table->unsignedInteger('qty_generated')->default(0);
            $table->decimal('unit_price', 18, 2);
            $table->decimal('total_cost', 18, 2);
            $table->enum('status', ['draft', 'paid', 'generated', 'failed', 'cancelled']);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['reseller_id', 'status'], 'voucher_batches_reseller_status_index');
            $table->index(['package_id'], 'voucher_batches_package_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_batches');
    }
};
