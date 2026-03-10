<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('balance', 18, 2)->default(0);
            $table->char('currency', 3)->default('IDR');
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        $now = now();
        $resellerIds = DB::table('users')->where('role', 'reseller')->pluck('id');

        foreach ($resellerIds as $resellerId) {
            DB::table('wallets')->insert([
                'user_id' => $resellerId,
                'balance' => 0,
                'currency' => 'IDR',
                'is_locked' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
