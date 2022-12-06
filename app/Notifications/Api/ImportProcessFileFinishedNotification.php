<?php

namespace App\Notifications\Api;

use App\Models\ImportProcess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Cache;

class ImportProcessFileFinishedNotification extends Notification
{
    use Queueable;

    public $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        \Log::debug("--------Notification importacion finalizada---------");
        //$urlFrontend = config('app.url_frontend');
        $importProcessesRecord = ImportProcess::query()->find($this->data["import-processes-id"]);

        return [
            'route' => "/imports/files/{$importProcessesRecord->getRouteKey()}/records",
            "icon" => "mdi-database-import",
            "color-icon" => "success",
            "title-notification" => "Importación finalizada - Temas",
            "message-notification" => "Archivo {$importProcessesRecord->name_file}",
            'message' => "Importacion de temas finalizado del archivo {$importProcessesRecord->name_file}"
        ];
    }

    public function toBroadcast ($notifiable) {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('import_process')->flush();

        return new BroadcastMessage([]);
    }
}
