<?php

namespace App\Jobs\Api\v1;

use App\Imports\Api\v1\QuestionsImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ImportQuestionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(Public $file, Public $userAuth)
    {
        //
    }

    public function handle()
    {
        (
        new QuestionsImport($this->userAuth, $this->file[0]->getClientOriginalName())
        )->import($this->file[0]);
    }

    public function failed(Throwable $exception)
    {
        // \Log::debug($exception->getMessage());
    }
}
