<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create manual MySQL database backup (.sql) into storage/app/backups';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $database = (string) config('database.connections.mysql.database');
        $username = (string) config('database.connections.mysql.username');
        $password = (string) config('database.connections.mysql.password');
        $host = (string) config('database.connections.mysql.host', '127.0.0.1');

        if ($database === '' || $username === '' || $host === '') {
            $this->error('Konfigurasi database MySQL tidak lengkap.');

            return self::FAILURE;
        }

        $backupDirectory = storage_path('app/backups');
        File::ensureDirectoryExists($backupDirectory);

        $timestamp = now()->format('Y-m-d-H-i-s');
        $filePath = $backupDirectory."/backup-{$timestamp}.sql";

        $passwordArgument = $password !== '' ? ' -p'.escapeshellarg($password) : '';
        $command = 'mysqldump -h '.escapeshellarg($host)
            .' -u '.escapeshellarg($username)
            .$passwordArgument
            .' '.escapeshellarg($database)
            .' > '.escapeshellarg($filePath);

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(300);

        $process->run();

        if (! $process->isSuccessful()) {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $errorOutput = trim($process->getErrorOutput());

            $this->error($errorOutput !== '' ? $errorOutput : 'Backup gagal dijalankan.');

            return self::FAILURE;
        }

        $this->info("Backup created at: {$filePath}");

        return self::SUCCESS;
    }
}
