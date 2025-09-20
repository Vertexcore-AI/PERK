<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DatabaseBackupService;
use App\Services\DatabaseRestoreService;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    protected $backupService;
    protected $restoreService;

    public function __construct(DatabaseBackupService $backupService, DatabaseRestoreService $restoreService)
    {
        $this->backupService = $backupService;
        $this->restoreService = $restoreService;
    }

    public function index(Request $request)
    {
        $externalPath = $request->get('external_path');
        $backups = $this->backupService->listBackups($externalPath);

        return view('backups.index', compact('backups', 'externalPath'));
    }

    public function create()
    {
        return view('backups.create');
    }

    public function store(Request $request)
    {
        Log::info('BackupController::store - Starting backup creation', [
            'external_path' => $request->input('external_path'),
            'user_ip' => $request->ip()
        ]);

        $request->validate([
            'external_path' => 'nullable|string'
        ]);

        $externalPath = $request->input('external_path');

        Log::info('BackupController::store - Calling backup service', [
            'external_path' => $externalPath
        ]);

        $result = $this->backupService->createBackup($externalPath);

        Log::info('BackupController::store - Backup service result', [
            'result' => $result,
            'success' => $result['success']
        ]);

        if ($result['success']) {
            Log::info('BackupController::store - Backup created successfully', [
                'backup_file' => $result['backup_file'],
                'size' => $result['size'] ?? 'unknown'
            ]);

            return redirect()->route('backups.index')
                ->with('success', 'Backup created successfully: ' . $result['backup_file']);
        } else {
            Log::error('BackupController::store - Backup creation failed', [
                'error' => $result['error']
            ]);

            return back()->withErrors(['error' => $result['error']]);
        }
    }

    public function download($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);

        if (!File::exists($backupPath)) {
            abort(404, 'Backup file not found');
        }

        return Response::download($backupPath);
    }

    public function destroy($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);
        $result = $this->backupService->deleteBackup($backupPath);

        if ($result['success']) {
            return redirect()->route('backups.index')
                ->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    public function info($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);

        if (!File::exists($backupPath)) {
            abort(404, 'Backup file not found');
        }

        $info = $this->backupService->getBackupInfo($backupPath);
        $validation = $this->backupService->validateBackup($backupPath);

        return view('backups.info', compact('filename', 'info', 'validation'));
    }

    public function restore(Request $request)
    {
        Log::info('BackupController::restore - Starting restore operation', [
            'backup_path' => $request->input('backup_path'),
            'create_current_backup' => $request->input('create_current_backup'),
            'user_ip' => $request->ip()
        ]);

        $request->validate([
            'backup_path' => 'required|string',
            'create_current_backup' => 'boolean'
        ]);

        $backupPath = $request->input('backup_path');
        $createCurrentBackup = $request->boolean('create_current_backup', true);

        Log::info('BackupController::restore - Validated inputs', [
            'backup_path' => $backupPath,
            'create_current_backup' => $createCurrentBackup,
            'backup_exists' => File::exists($backupPath)
        ]);

        $result = $this->restoreService->restoreFromBackup($backupPath, $createCurrentBackup);

        Log::info('BackupController::restore - Restore service result', [
            'result' => $result,
            'success' => $result['success']
        ]);

        $this->restoreService->logRestoreOperation($backupPath, $result);

        if ($result['success']) {
            Log::info('BackupController::restore - Database restored successfully', [
                'backup_path' => $backupPath
            ]);

            return redirect()->route('backups.index')
                ->with('success', 'Database restored successfully from backup');
        } else {
            Log::error('BackupController::restore - Database restore failed', [
                'backup_path' => $backupPath,
                'error' => $result['error']
            ]);

            return back()->withErrors(['error' => $result['error']]);
        }
    }

    public function restoreFromExternal(Request $request)
    {
        Log::info('BackupController::restoreFromExternal - Starting external restore operation', [
            'external_backup_path' => $request->input('external_backup_path'),
            'create_current_backup' => $request->input('create_current_backup'),
            'user_ip' => $request->ip()
        ]);

        $request->validate([
            'external_backup_path' => 'required|string',
            'create_current_backup' => 'boolean'
        ]);

        $externalBackupPath = $request->input('external_backup_path');
        $createCurrentBackup = $request->boolean('create_current_backup', true);

        Log::info('BackupController::restoreFromExternal - Validated inputs', [
            'external_backup_path' => $externalBackupPath,
            'create_current_backup' => $createCurrentBackup,
            'external_backup_exists' => File::exists($externalBackupPath)
        ]);

        $result = $this->restoreService->restoreFromExternalBackup($externalBackupPath, $createCurrentBackup);

        Log::info('BackupController::restoreFromExternal - Restore service result', [
            'result' => $result,
            'success' => $result['success']
        ]);

        $this->restoreService->logRestoreOperation($externalBackupPath, $result);

        if ($result['success']) {
            Log::info('BackupController::restoreFromExternal - External restore completed successfully', [
                'external_backup_path' => $externalBackupPath
            ]);

            return redirect()->route('backups.index')
                ->with('success', 'Database restored successfully from external backup');
        } else {
            Log::error('BackupController::restoreFromExternal - External restore failed', [
                'external_backup_path' => $externalBackupPath,
                'error' => $result['error']
            ]);

            return back()->withErrors(['error' => $result['error']]);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:102400'
        ]);

        $uploadedFile = $request->file('backup_file');
        $filename = 'uploaded_backup_' . time() . '.zip';
        $backupPath = storage_path('app/backups/' . $filename);

        $uploadedFile->move(storage_path('app/backups'), $filename);

        $validation = $this->backupService->validateBackup($backupPath);

        if (!$validation['valid']) {
            File::delete($backupPath);
            return back()->withErrors(['error' => 'Invalid backup file: ' . $validation['error']]);
        }

        return redirect()->route('backups.index')
            ->with('success', 'Backup file uploaded successfully: ' . $filename);
    }

    public function cleanup(Request $request)
    {
        $request->validate([
            'keep_days' => 'integer|min:1|max:365'
        ]);

        $keepDays = $request->input('keep_days', 30);
        $result = $this->backupService->cleanupOldBackups($keepDays);

        if ($result['success']) {
            return redirect()->route('backups.index')
                ->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    public function restoreHistory()
    {
        $history = $this->restoreService->getRestoreHistory();
        return view('backups.restore-history', compact('history'));
    }

    public function validate(Request $request)
    {
        $request->validate([
            'backup_path' => 'required|string'
        ]);

        $backupPath = $request->input('backup_path');
        $result = $this->backupService->validateBackup($backupPath);

        return response()->json($result);
    }
}
