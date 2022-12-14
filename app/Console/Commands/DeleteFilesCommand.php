<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old audio files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Clean locally stored audio result files based on the set date.
     *
     * @return int
     */
    public function handle()
    {
        $files = Storage::disk('audio')->listContents();

        $days = config('tts.clean_storage');

        if ($days != 'never') {
            foreach($files as $file) {
                if ($file['type'] == 'file' && $file['timestamp'] < now()->subDays($days)->getTimestamp()) {
                    Storage::disk('audio')->delete($file['path']);
                }            
            }
        }
        
    }
}
