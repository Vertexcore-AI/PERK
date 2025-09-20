<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use ZipArchive;

class DatabaseBackupService
{
    protected $backupPath;
    protected $databasePath;

    public function __construct()
    {
        $this->databasePath = database_path('database.sqlite');
        $this->backupPath = storage_path('app/backups');

        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    public function createBackup($externalPath = null)
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupFileName = "database_backup_{$timestamp}.sqlite";
            $zipFileName = "perk_backup_{$timestamp}.zip";

            $localBackupPath = $this->backupPath . DIRECTORY_SEPARATOR . $backupFileName;
            $localZipPath = $this->backupPath . DIRECTORY_SEPARATOR . $zipFileName;

            if (!File::exists($this->databasePath)) {
                throw new Exception('Database file not found: ' . $this->databasePath);
            }

            File::copy($this->databasePath, $localBackupPath);

            $zip = new ZipArchive();
            if ($zip->open($localZipPath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($localBackupPath, $backupFileName);

                $backupInfo = [
                    'backup_date' => Carbon::now()->toISOString(),
                    'database_size' => File::size($this->databasePath),
                    'laravel_version' => app()->version(),
                    'backup_type' => 'sqlite_database',
                    'original_name' => 'database.sqlite'
                ];

                $zip->addFromString('backup_info.json', json_encode($backupInfo, JSON_PRETTY_PRINT));
                $zip->close();

                File::delete($localBackupPath);
            } else {
                throw new Exception('Unable to create backup archive');
            }

            if ($externalPath) {
                $this->copyToExternalLocation($localZipPath, $externalPath, $zipFileName);
            }

            return [
                'success' => true,
                'backup_file' => $zipFileName,
                'local_path' => $localZipPath,
                'size' => File::size($localZipPath),
                'timestamp' => $timestamp
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function listBackups($externalPath = null)
    {
        $backups = [];

        $localBackups = File::glob($this->backupPath . '/perk_backup_*.zip');
        foreach ($localBackups as $backup) {
            $backups[] = [
                'filename' => basename($backup),
                'path' => $backup,
                'size' => File::size($backup),
                'created_at' => Carbon::createFromTimestamp(File::lastModified($backup)),
                'location' => 'local'
            ];
        }

        if ($externalPath && File::exists($externalPath)) {
            $externalBackups = File::glob($externalPath . '/perk_backup_*.zip');
            foreach ($externalBackups as $backup) {
                $backups[] = [
                    'filename' => basename($backup),
                    'path' => $backup,
                    'size' => File::size($backup),
                    'created_at' => Carbon::createFromTimestamp(File::lastModified($backup)),
                    'location' => 'external'
                ];
            }
        }

        return collect($backups)->sortByDesc('created_at')->values()->all();
    }

    public function deleteBackup($backupPath)
    {
        try {
            if (File::exists($backupPath)) {
                File::delete($backupPath);
                return ['success' => true, 'message' => 'Backup deleted successfully'];
            }
            return ['success' => false, 'error' => 'Backup file not found'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function cleanupOldBackups($keepDays = 30)
    {
        try {
            $cutoffDate = Carbon::now()->subDays($keepDays);
            $deleted = 0;

            $backups = File::glob($this->backupPath . '/perk_backup_*.zip');
            foreach ($backups as $backup) {
                $fileDate = Carbon::createFromTimestamp(File::lastModified($backup));
                if ($fileDate->lt($cutoffDate)) {
                    File::delete($backup);
                    $deleted++;
                }
            }

            return [
                'success' => true,
                'deleted_count' => $deleted,
                'message' => "Deleted {$deleted} old backup(s)"
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function validateBackup($backupPath)
    {
        try {
            if (!File::exists($backupPath)) {
                return ['valid' => false, 'error' => 'Backup file not found'];
            }

            $zip = new ZipArchive();
            if ($zip->open($backupPath) === TRUE) {
                $hasDatabase = false;
                $hasInfo = false;

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (strpos($filename, 'database_backup_') === 0 && pathinfo($filename, PATHINFO_EXTENSION) === 'sqlite') {
                        $hasDatabase = true;
                    }
                    if ($filename === 'backup_info.json') {
                        $hasInfo = true;
                    }
                }

                $zip->close();

                if ($hasDatabase && $hasInfo) {
                    return ['valid' => true, 'message' => 'Backup file is valid'];
                } else {
                    return ['valid' => false, 'error' => 'Invalid backup structure'];
                }
            } else {
                return ['valid' => false, 'error' => 'Unable to read backup archive'];
            }
        } catch (Exception $e) {
            return ['valid' => false, 'error' => $e->getMessage()];
        }
    }

    protected function copyToExternalLocation($localPath, $externalPath, $fileName)
    {
        if (!File::exists($externalPath)) {
            File::makeDirectory($externalPath, 0755, true);
        }

        $externalFilePath = $externalPath . DIRECTORY_SEPARATOR . $fileName;
        File::copy($localPath, $externalFilePath);
    }

    public function getBackupInfo($backupPath)
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
}