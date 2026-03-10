<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('vouchers')
            ->select(['id', 'password'])
            ->whereNotNull('password')
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $password = (string) $row->password;

                    if ($password === '') {
                        continue;
                    }

                    try {
                        Crypt::decryptString($password);

                        // Sudah terenkripsi, skip.
                        continue;
                    } catch (DecryptException) {
                        DB::table('vouchers')
                            ->where('id', $row->id)
                            ->update([
                                'password' => Crypt::encryptString($password),
                            ]);
                    }
                }
            }, 'id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('vouchers')
            ->select(['id', 'password'])
            ->whereNotNull('password')
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $password = (string) $row->password;

                    if ($password === '') {
                        continue;
                    }

                    try {
                        DB::table('vouchers')
                            ->where('id', $row->id)
                            ->update([
                                'password' => Crypt::decryptString($password),
                            ]);
                    } catch (DecryptException) {
                        // Jika bukan ciphertext valid, biarkan apa adanya.
                    }
                }
            }, 'id');
    }
};
