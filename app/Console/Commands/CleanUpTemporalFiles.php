<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanUpTemporalFiles extends Command
{
    public static $seconds = 10; // 1 * 3600; // 1h
    protected $signature = 'files:clean';
    protected $description = 'Delete old temporal files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $temporalFolderPath = public_path('temp'); // Replace with your temporal folder path

        if (!File::exists($temporalFolderPath)) {
            $this->info('Temporal folder does not exist.');
            return;
        }

        $files = File::allFiles($temporalFolderPath);

        foreach ($files as $file) {
            $fileModificationTime = $file->getMTime();
            $currentTime = now()->timestamp;

            // Check if the file is older than 2 hours (7200 seconds)
            if (($currentTime - $fileModificationTime) > self::$seconds) {
                $this->info('Deleting:' . $file->getFilename());
                File::delete($file->getPathname());
            }
        }

        $this->info('Old temporal materials have been deleted.');

    }
}