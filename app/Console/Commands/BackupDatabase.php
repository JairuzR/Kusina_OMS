<?php

namespace App\Console\Commands;

use App\Mail\BackupCompletedMail;
use App\Models\AuditLog;
use App\Models\Backup;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature   = 'backup:database {--type=scheduled : manual or scheduled}';
    protected $description = 'Create a MySQL dump of the database and store it locally';

    public function handle(): int
    {
        $type     = $this->option('type');
        $filename = 'backup_' . now()->format('Y-m-d_His') . '_' . $type . '.sql';
        $path     = 'backups/' . $filename;
        $fullPath = storage_path('app/' . $path);

        Storage::makeDirectory('backups');

        $backup = Backup::create([
            'filename'   => $filename,
            'disk'       => 'local',
            'path'       => $path,
            'size'       => 0,
            'type'       => $type,
            'status'     => 'pending',
            'created_by' => null,
        ]);

        try {
            $this->runMysqlDump($fullPath);

            $size = file_exists($fullPath) ? filesize($fullPath) : 0;

            $backup->update([
                'status' => 'completed',
                'size'   => $size,
            ]);

            $this->info("Backup completed: {$filename} (" . number_format($size / 1024, 1) . " KB)");

            $this->enforceRetention();
            $this->sendNotification($backup, true);

            AuditLog::create([
                'user_id'     => null,
                'action'      => 'backup',
                'module'      => 'backup',
                'description' => "Database backup completed: {$filename}",
                'ip_address'  => '127.0.0.1',
                'user_agent'  => 'Artisan/Scheduler',
            ]);

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $backup->update(['status' => 'failed', 'notes' => $e->getMessage()]);
            $this->error("Backup failed: " . $e->getMessage());
            $this->sendNotification($backup, false, $e->getMessage());

            return self::FAILURE;
        }
    }

    private function findMysqldump(): string
    {
        // 1. Check if it's already in PATH
        $which = PHP_OS_FAMILY === 'Windows'
            ? shell_exec('where mysqldump 2>NUL')
            : shell_exec('which mysqldump 2>/dev/null');

        if (!empty(trim($which ?? ''))) {
            return 'mysqldump';
        }

        // 2. Common Windows installation paths
        $windowsPaths = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\xampp7\\mysql\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.4\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.4\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
            'C:\\wamp\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
        ];

        // Also scan Laragon's mysql folder dynamically
        foreach (['C:\\laragon\\bin\\mysql'] as $dir) {
            if (is_dir($dir)) {
                $versions = glob($dir . '\\*', GLOB_ONLYDIR);
                foreach ($versions as $v) {
                    $windowsPaths[] = $v . '\\bin\\mysqldump.exe';
                }
            }
        }

        foreach ($windowsPaths as $path) {
            if (file_exists($path)) {
                return '"' . $path . '"';
            }
        }

        // 3. Linux/Mac common paths
        $unixPaths = [
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/local/mysql/bin/mysqldump',
        ];

        foreach ($unixPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException(
            'mysqldump not found. Add your MySQL bin folder to the system PATH, ' .
            'or set MYSQLDUMP_PATH in your .env file.'
        );
    }

    private function runMysqlDump(string $fullPath): void
    {
        // Allow override via .env: MYSQLDUMP_PATH=C:\xampp\mysql\bin\mysqldump.exe
        $binary = env('MYSQLDUMP_PATH')
            ? '"' . env('MYSQLDUMP_PATH') . '"'
            : $this->findMysqldump();

        $db       = config('database.connections.mysql');
        $host     = $db['host'];
        $port     = $db['port'];
        $dbname   = $db['database'];
        $username = $db['username'];
        $password = $db['password'];

        // Ensure the backups directory physically exists before writing .my.cnf
        $backupsDir = storage_path('app/backups');
        if (!is_dir($backupsDir)) {
            mkdir($backupsDir, 0755, true);
        }

        // Write a temporary MySQL options file to avoid password on the command line
        $cnfPath    = $backupsDir . DIRECTORY_SEPARATOR . '.my.cnf';
        $cnfContent = "[client]\npassword=" . $password . "\n";
        file_put_contents($cnfPath, $cnfContent);
        chmod($cnfPath, 0600);

        $outPath = escapeshellarg($fullPath);
        $command = sprintf(
            '%s --defaults-extra-file=%s --host=%s --port=%s --user=%s %s > %s 2>&1',
            $binary,
            escapeshellarg($cnfPath),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($dbname),
            $outPath
        );

        exec($command, $output, $exitCode);

        // Clean up credentials file immediately
        @unlink($cnfPath);

        if ($exitCode !== 0) {
            $outputStr = implode(' ', $output);
            throw new \RuntimeException(
                "mysqldump failed (exit {$exitCode}). " .
                ($outputStr ? "Output: {$outputStr}" : "Check that your DB credentials in .env are correct.")
            );
        }

        // Sanity check — an empty file means something went wrong silently
        if (!file_exists($fullPath) || filesize($fullPath) < 100) {
            throw new \RuntimeException(
                'Backup file was created but appears empty. Check DB credentials and permissions.'
            );
        }
    }

    private function enforceRetention(): void
    {
        $old = Backup::where('created_at', '<', now()->subDays(30))->get();

        foreach ($old as $backup) {
            if (Storage::exists($backup->path)) {
                Storage::delete($backup->path);
            }
            $backup->delete();
        }

        if ($old->count() > 0) {
            $this->line("Retention: removed {$old->count()} old backup(s).");
        }
    }

    private function sendNotification(Backup $backup, bool $success, string $error = ''): void
    {
        $recipient = Setting::get('backup_email')
            ?? Setting::get('admin_email')
            ?? config('mail.from.address');

        if (empty($recipient)) {
            return;
        }

        try {
            Mail::to($recipient)->send(new BackupCompletedMail($backup, $success, $error));
        } catch (\Throwable $e) {
            $this->warn("Could not send backup email: " . $e->getMessage());
        }
    }
}