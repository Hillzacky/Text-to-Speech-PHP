<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use League\Flysystem\Adapter\Local;
use Illuminate\Support\Facades\Storage;
use Artisan;
use Exception;
use Log;

class BackupController extends Controller
{
    /**
     * Display all backups
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $files = $disk->files(config('backup.backup.name'));
        $backups = [];

        foreach ($files as $k => $f) {

            if (substr($f, -4) == '.zip' && $disk->exists($f)) {
                $backups[] = [
                    'file_path' => $f,
                    'file_name' => str_replace(config('backup.backup.name') . '/', '', $f),
                    'file_size' => $disk->size($f),
                    'last_modified' => $disk->lastModified($f),
                    'disk' => $disk,
                ];
            }
        }

        $backups = array_reverse($backups);

        return view('admin.settings.backup.index', compact('backups'));
    }


    /**
     * Create a new backup
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {

            Artisan::call('backup:run', ['--only-db' => true, '--disable-notifications' => true]);
            $output = Artisan::output();
          
            Log::info("Database Backup -- new mysql db backup started from admin interface \r\n" . $output);

            return redirect()->back()->with('success', 'New DB backup was created successfully');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'There was an error during backup creation');;
        }
    }


    /**
     * Download a backup file
     *
     * @return \Illuminate\Http\Response
     */
    public function download($file_name)
    {

        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $file_name = config('backup.backup.name') . '/' . $file_name;
        $adapter = $disk->getDriver()->getAdapter();

        if ($adapter instanceof Local) {
            $storage_path = $disk->getDriver()->getAdapter()->getPathPrefix();

            if ($disk->exists($file_name)) {
                return response()->download($storage_path.$file_name);
            } else {
                abort(404);
            }
        } else {
            abort(404);
        }
    }


    /**
     * Delete backup file 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($file_name)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);

        if ($disk->exists(config('backup.backup.name') . '/' . $file_name)) {
            
            $disk->delete(config('backup.backup.name') . '/' . $file_name);
            return redirect()->back()->with('success', 'DB backup was successfully deleted');

        } else {
            abort(404);
        }
    }
}
