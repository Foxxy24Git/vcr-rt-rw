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
        Schema::table('vouchers', function (Blueprint $table): void {
            $table->string('status', 20)->default('READY')->change();
            $table->string('uptime')->nullable()->after('status');
            $table->timestamp('last_sync_at')->nullable()->after('uptime');
            $table->index(['status', 'last_sync_at'], 'vouchers_status_last_sync_at_index');
        });

        DB::table('vouchers')->update([
            'status' => DB::raw("
                CASE LOWER(status)
                    WHEN 'ready' THEN 'READY'
                    WHEN 'used' THEN 'USED'
                    WHEN 'expired' THEN 'EXPIRED'
                    WHEN 'sold' THEN 'ACTIVE'
                    WHEN 'revoked' THEN 'DISABLED'
                    ELSE UPPER(status)
                END
            "),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('vouchers')->update([
            'status' => DB::raw("
                CASE UPPER(status)
                    WHEN 'READY' THEN 'ready'
                    WHEN 'USED' THEN 'used'
                    WHEN 'EXPIRED' THEN 'expired'
                    WHEN 'ACTIVE' THEN 'sold'
                    WHEN 'DISABLED' THEN 'revoked'
                    ELSE 'ready'
                END
            "),
        ]);

        Schema::table('vouchers', function (Blueprint $table): void {
            $table->enum('status', ['ready', 'sold', 'used', 'expired', 'revoked'])->default('ready')->change();
            $table->dropIndex('vouchers_status_last_sync_at_index');
            $table->dropColumn(['uptime', 'last_sync_at']);
        });
    }
};
