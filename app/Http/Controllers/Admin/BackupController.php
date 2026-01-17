<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\AuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->getBackupFiles();
        
        return view('admin.backup.index', compact('backups'));
    }

    public function create()
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_His');
            $filename = "backup_{$timestamp}.sql";
            
            // Get database configuration
            $database = config('database.connections.' . config('database.default'));
            $dbName = $database['database'];
            $dbUser = $database['username'];
            $dbPass = $database['password'];
            $dbHost = $database['host'];
            
            // Create backup directory if it doesn't exist
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $backupPath = $backupDir . '/' . $filename;
            
            // For SQLite
            if ($database['driver'] === 'sqlite') {
                $dbPath = database_path($database['database']);
                if (file_exists($dbPath)) {
                    copy($dbPath, $backupPath);
                }
            } else {
                // For MySQL/PostgreSQL
                $command = sprintf(
                    'mysqldump -h %s -u %s -p%s %s > %s 2>&1',
                    escapeshellarg($dbHost),
                    escapeshellarg($dbUser),
                    escapeshellarg($dbPass),
                    escapeshellarg($dbName),
                    escapeshellarg($backupPath)
                );
                exec($command, $output, $returnVar);
                
                if ($returnVar !== 0) {
                    throw new \Exception('Backup failed: ' . implode("\n", $output));
                }
            }
            
            // Log the backup creation
            AuditHelper::log('create', "Database backup created: {$filename}");
            
            return back()->with('success', "Backup created successfully: {$filename}");
        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string',
        ]);
        
        try {
            $backupFile = $request->backup_file;
            $backupPath = storage_path('app/backups/' . $backupFile);
            
            if (!file_exists($backupPath)) {
                return back()->with('error', 'Backup file not found.');
            }
            
            $database = config('database.connections.' . config('database.default'));
            
            // For SQLite
            if ($database['driver'] === 'sqlite') {
                $dbPath = database_path($database['database']);
                if (file_exists($dbPath)) {
                    copy($backupPath, $dbPath);
                }
            } else {
                // For MySQL/PostgreSQL
                $dbName = $database['database'];
                $dbUser = $database['username'];
                $dbPass = $database['password'];
                $dbHost = $database['host'];
                
                $command = sprintf(
                    'mysql -h %s -u %s -p%s %s < %s 2>&1',
                    escapeshellarg($dbHost),
                    escapeshellarg($dbUser),
                    escapeshellarg($dbPass),
                    escapeshellarg($dbName),
                    escapeshellarg($backupPath)
                );
                exec($command, $output, $returnVar);
                
                if ($returnVar !== 0) {
                    throw new \Exception('Restore failed: ' . implode("\n", $output));
                }
            }
            
            // Log the restore
            AuditHelper::log('update', "Database restored from backup: {$backupFile}");
            
            return back()->with('success', "Database restored successfully from: {$backupFile}");
        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($backupPath)) {
            abort(404, 'Backup file not found.');
        }
        
        AuditHelper::log('view', "Backup file downloaded: {$filename}");
        
        return response()->download($backupPath);
    }

    public function destroy($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);
        
        if (file_exists($backupPath)) {
            unlink($backupPath);
            AuditHelper::log('delete', "Backup file deleted: {$filename}");
            return back()->with('success', 'Backup file deleted successfully.');
        }
        
        return back()->with('error', 'Backup file not found.');
    }

    private function getBackupFiles()
    {
        $backupDir = storage_path('app/backups');
        $files = [];
        
        if (is_dir($backupDir)) {
            $fileList = glob($backupDir . '/backup_*.sql');
            foreach ($fileList as $file) {
                $files[] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'created_at' => Carbon::createFromTimestamp(filemtime($file)),
                ];
            }
            
            // Sort by created_at descending
            usort($files, function($a, $b) {
                return $b['created_at'] <=> $a['created_at'];
            });
        }
        
        return $files;
    }
}
