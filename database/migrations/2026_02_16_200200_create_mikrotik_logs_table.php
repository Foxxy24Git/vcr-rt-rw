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
        Schema::create('mikrotik_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('server_id')->nullable()->constrained('mikrotik_servers')->nullOnDelete();
            $table->string('action');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->enum('status', ['success', 'failed', 'simulated']);
            $table->string('message')->nullable();
            $table->timestamps();

            $table->index(['server_id', 'action', 'status', 'created_at'], 'mikrotik_logs_server_action_status_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_logs');
    }
};
