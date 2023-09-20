<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TemporalFileClean extends Command
{
    public static $seconds = 2 * 3600; // 2 * h
    protected $signature = 'files:clean';
    protected $description = 'Delete old temporal files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->deleteFiles(public_path('temp'));
    }

    public function deleteFiles($path)
    {
        if (!File::isDirectory($path)) {
            $this->log($path . ' folder does not exist.');
            return false;
        }

        $files = File::allFiles($path);

        foreach ($files as $file) {

            $fileModificationTime = $file->getMTime();
            $currentTime = now()->timestamp;


            if (($currentTime - $fileModificationTime) > self::$seconds) {
                $this->log('Deleting:' . $file->getFilename());
                File::delete($file->getPathname());
            }
        }

        $this->log('Old temporal materials have been deleted.');
        return true;
    }

    private function log($content)
    {
        if (config('app.env') === 'testing') {
            return;
        }
        $this->info($content);
    }
}