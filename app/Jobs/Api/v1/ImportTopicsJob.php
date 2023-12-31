<?php

namespace App\Jobs\Api\v1;

use App\Imports\Api\v1\TopicsImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ImportTopicsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public $file, public $userAuth)
    {
        //
    }

    public function handle()
    {
        \Log::debug(
            '------------------ DDD'
        );

        (
            new TopicsImport($this->userAuth, $this->file->getClientOriginalName())
        )->import($this->file);
    }

    public function failed(Throwable $exception)
    {
        // \Log::debug($exception->getMessage());
    }
}