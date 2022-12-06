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
        //$urlFrontend = config('app.url_frontend');
        $importProcessesRecord = ImportProcess::query()->find($this->data["import-processes-id"]);
        $route = $this->data["route"] ?? "/imports/files/{$importProcessesRecord->getRouteKey()}/records";
        $icon = $this->data["icon"] ?? "mdi-database-import";
        $colorIcon = $this->data["color-icon"] ?? "success";
        $titleNotification = $this->data["title-notification"];
        $messageNotification = $this->data["message-notification"] ?? "Archivo {$importProcessesRecord->name_file}";
        $description = $this->data["description"];

        return [
            'route' => $route,
            "icon" => $icon,
            "color-icon" => $colorIcon,
            "title-notification" => $titleNotification,
            "message-notification" => $messageNotification,
            'description' => $description
        ];
    }

    public function toBroadcast ($notifiable) {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('import_process')->flush();

        return new BroadcastMessage([]);
    }
}
