<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::with('creator')->latest()->paginate(15);

        $stats = [
            'total'     => Backup::count(),
            'completed' => Backup::where('status', 'completed')->count(),
            'failed'    => Backup::where('status', 'failed')->count(),
            'size'      => Backup::where('status', 'completed')->sum('size'),
        ];

        return view('backups.index', compact('backups', 'stats'));
    }

    public function store(Request $request)
    {
        // Create the Backup record first so we can assign created_by
        $backup = Backup::create([
            'filename'   => 'pending_' . now()->format('Y-m-d_His') . '_manual.sql',
            'disk'       => 'local',
            'path'       => 'backups/pending',
            'size'       => 0,
            'type'       => 'manual',
            'status'     => 'pending',
            'created_by' => auth()->id(),
        ]);

        // Run the command synchronously for manual triggers
        Artisan::call('backup:database', ['--type' => 'manual']);
        $output = Artisan::output();

        // Refresh from DB — command updates the latest pending record
        $latest = Backup::where('type', 'manual')->latest()->first();

        if ($latest && $latest->status === 'completed') {
            $backup->delete(); // remove the placeholder
            return back()->with('success', 'Backup completed successfully. File: ' . $latest->filename);
        }

        return back()->with('error', 'Backup failed. Check logs for details.');
    }

    public function download(Backup $backup)
    {
        if ($backup->status !== 'completed') {
            return back()->with('error', 'Only completed backups can be downloaded.');
        }

        $fullPath = storage_path('app/' . $backup->path);

        if (!file_exists($fullPath)) {
            return back()->with('error', 'Backup file not found on disk.');
        }

        return response()->download(
            $fullPath,
            $backup->filename,
            [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $backup->filename . '"',
            ]
        );
    }

    public function destroy(Backup $backup)
    {
        if (Storage::exists($backup->path)) {
            Storage::delete($backup->path);
        }

        $backup->delete();

        return back()->with('success', 'Backup deleted.');
    }

    public function verify(Backup $backup)
    {
        if ($backup->status !== 'completed') {
            return back()->with('error', 'Cannot verify a non-completed backup.');
        }

        $exists    = Storage::exists($backup->path);
        $sizeMatch = $exists && Storage::size($backup->path) === $backup->size;

        if ($exists && $sizeMatch) {
            return back()->with('success', "Backup verified — file exists and size matches ({$backup->formatted_size}).");
        }

        if ($exists && !$sizeMatch) {
            return back()->with('warning', 'Backup file exists but size mismatch detected. The file may be corrupted.');
        }

        return back()->with('error', 'Backup file is missing from disk.');
    }
}