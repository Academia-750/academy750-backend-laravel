<?php

namespace App\Jobs\Api\v1;

use App\Imports\Api\v1\SubtopicsImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ImportSubtopicsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(Public $file, Public $userAuth)
    {
        //
    }

    public function handle()
    {
        (
        new SubtopicsImport($this->userAuth, $this->file->getClientOriginalName())
        )->import($this->file);
    }

    public function failed(Throwable $exception)
    {
        // \Log::debug($exception->getMessage());
    }
}
