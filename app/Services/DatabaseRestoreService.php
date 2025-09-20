<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Exception;
use ZipArchive;

class DatabaseRestoreService
{
    protected $backupPath;
    protected $databasePath;
    protected $tempPath;

    public function __construct()
    {
        $this->databasePath = database_path('database.sqlite');
        $this->backupPath = storage_path('app/backups');
        $this->tempPath = storage_path('app/temp');

        if (!File::exists($this->tempPath)) {
            File::makeDirectory($this->tempPath, 0755, true);
        }
    }

    public function restoreFromBackup($backupPath, $createCurrentBackup = true)
    {
        try {
            if (!File::exists($backupPath)) {
                throw new Exception('Backup file not found: ' . $backupPath);
            }

            $validation = $this->validateBackupFile($backupPath);
            if (!$validation['valid']) {
                throw new Exception('Invalid backup file: ' . $validation['error']);
            }

            if ($createCurrentBackup) {
                $currentBackupResult = $this->createCurrentDatabaseBackup();
                if (!$currentBackupResult['success']) {
                    throw new Exception('Failed to create current database backup: ' . $currentBackupResult['error']);
                }
            }

            $extractedDbPath = $this->extractDatabaseFromBackup($backupPath);
            if (!$extractedDbPath) {
                throw new Exception('Failed to extract database from backup');
            }

            $this->disconnectDatabase();

            // Try multiple times to handle file locking
            $maxAttempts = 5;
            $oldDbPath = null;

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                try {
                    if (File::exists($this->databasePath)) {
                        $oldDbPath = $this->databasePath . '.old.' . time() . '.' . $attempt;
                        File::move($this->databasePath, $oldDbPath);
                    }

                    File::copy($extractedDbPath, $this->databasePath);
                    break; // Success
                } catch (Exception $e) {
                    if ($attempt === $maxAttempts) {
                        throw new Exception('Failed to replace database file after ' . $maxAttempts . ' attempts: ' . $e->getMessage());
                    }

                    // Wait and try to disconnect again
                    usleep(200000); // 200ms
                    $this->disconnectDatabase();
                }
            }

            File::delete($extractedDbPath);

            $this->reconnectDatabase();

            $this->verifyRestoredDatabase();

            if (isset($oldDbPath) && File::exists($oldDbPath)) {
                File::delete($oldDbPath);
            }

            return [
                'success' => true,
                'message' => 'Database restored successfully',
                'restored_at' => Carbon::now()->toISOString(),
                'backup_info' => $this->getBackupInfo($backupPath)
            ];

        } catch (Exception $e) {
            $this->reconnectDatabase();

            if (isset($oldDbPath) && File::exists($oldDbPath)) {
                if (File::exists($this->databasePath)) {
                    File::delete($this->databasePath);
                }
                File::move($oldDbPath, $this->databasePath);
            }

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function restoreFromExternalBackup($externalBackupPath, $createCurrentBackup = true)
    {
        try {
            if (!File::exists($externalBackupPath)) {
                throw new Exception('External backup file not found: ' . $externalBackupPath);
            }

            $tempBackupPath = $this->tempPath . DIRECTORY_SEPARATOR . 'temp_restore_' . time() . '.zip';
            File::copy($externalBackupPath, $tempBackupPath);

            $result = $this->restoreFromBackup($tempBackupPath, $createCurrentBackup);

            File::delete($tempBackupPath);

            return $result;

        } catch (Exception $e) {
            if (isset($tempBackupPath) && File::exists($tempBackupPath)) {
                File::delete($tempBackupPath);
            }

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function createPreRestoreBackup()
    {
        $backupService = new DatabaseBackupService();
        return $backupService->createBackup();
    }

    public function validateBackupFile($backupPath)
    {
        try {
            if (!File::exists($backupPath)) {
                return ['valid' => false, 'error' => 'Backup file not found'];
            }

            $zip = new ZipArchive();
            if ($zip->open($backupPath) === TRUE) {
                $hasDatabase = false;
                $hasInfo = false;
                $databaseFileName = '';

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (strpos($filename, 'database_backup_') === 0 && pathinfo($filename, PATHINFO_EXTENSION) === 'sqlite') {
                        $hasDatabase = true;
                        $databaseFileName = $filename;
                    }
                    if ($filename === 'backup_info.json') {
                        $hasInfo = true;
                    }
                }

                $zip->close();

                if (!$hasDatabase) {
                    return ['valid' => false, 'error' => 'No SQLite database found in backup'];
                }

                if (!$hasInfo) {
                    return ['valid' => false, 'error' => 'No backup information found'];
                }

                return [
                    'valid' => true,
                    'database_file' => $databaseFileName,
                    'message' => 'Backup file is valid'
                ];
            } else {
                return ['valid' => false, 'error' => 'Unable to read backup archive'];
            }
        } catch (Exception $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }

    protected function extractDatabaseFromBackup($backupPath)
    {
        try {
            $zip = new ZipArchive();
            if ($zip->open($backupPath) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (strpos($filename, 'database_backup_') === 0 && pathinfo($filename, PATHINFO_EXTENSION) === 'sqlite') {
                        $extractedPath = $this->tempPath . DIRECTORY_SEPARATOR . 'extracted_' . time() . '.sqlite';
                        $result = $zip->extractTo($this->tempPath, $filename);
                        if ($result) {
                            $sourcePath = $this->tempPath . DIRECTORY_SEPARATOR . $filename;
                            File::move($sourcePath, $extractedPath);
                            $zip->close();
                            return $extractedPath;
                        }
                    }
                }
                $zip->close();
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function disconnectDatabase()
    {
        try {
            // Close all database connections
            DB::disconnect('sqlite');
            DB::purge('sqlite');

            // Force garbage collection to release file handles
            gc_collect_cycles();

            // Small delay to ensure file handles are released
            usleep(100000); // 100ms
        } catch (Exception $e) {
            // Log but continue
            \Log::warning('DatabaseRestoreService::disconnectDatabase - Warning during disconnect', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function reconnectDatabase()
    {
        try {
            DB::reconnect('sqlite');
        } catch (Exception $e) {
        }
    }

    protected function verifyRestoredDatabase()
    {
        try {
            DB::connection('sqlite')->getPdo();
            $tables = DB::connection('sqlite')->select("SELECT name FROM sqlite_master WHERE type='table'");

            if (empty($tables)) {
                throw new Exception('Restored database appears to be empty');
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('Database verification failed: ' . $e->getMessage());
        }
    }

    protected function createCurrentDatabaseBackup()
    {
        $backupService = new DatabaseBackupService();
        return $backupService->createBackup();
    }

    protected function getBackupInfo($backupPath)
    {
        try {
            $zip = new ZipArchive();
            if ($zip->open($backupPath) === TRUE) {
                $infoContent = $zip->getFromName('backup_info.json');
                $zip->close();

                if ($infoContent) {
                    return json_decode($infoContent, true);
                }
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getRestoreHistory()
    {
        $historyFile = storage_path('app/restore_history.json');

        if (File::exists($historyFile)) {
            $history = json_decode(File::get($historyFile), true);
            return $history ?: [];
        }

        return [];
    }

    public function logRestoreOperation($backupPath, $result)
    {
        $historyFile = storage_path('app/restore_history.json');
        $history = $this->getRestoreHistory();

        $logEntry = [
            'timestamp' => Carbon::now()->toISOString(),
            'backup_file' => basename($backupPath),
            'success' => $result['success'],
            'message' => $result['success'] ? 'Restore completed successfully' : $result['error']
        ];

        array_unshift($history, $logEntry);
        $history = array_slice($history, 0, 50);

        File::put($historyFile, json_encode($history, JSON_PRETTY_PRINT));
    }
}