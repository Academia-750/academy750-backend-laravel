<?php

namespace App\Notifications\Api;

use App\Models\ImportProcess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Cache;

class ImportProcessFileFinishedNotification extends Notification/* implements ShouldQueue*/
{
    use Queueable;

    public $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        //$urlFrontend = config('app.url_frontend');
        $importProcessesRecord = ImportProcess::query()->findOrFail($this->data["import-processes-id"]);
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

    public function toMail($notifiable): MailMessage
    {
        $importProcessesRecord = ImportProcess::query()->findOrFail($this->data["import-processes-id"]);
        $titleNotification = $this->data["title-notification"];

        return (new MailMessage)
            //->from( config('mail.from.address') )
            ->subject("Importación finalizada - {$importProcessesRecord->name_file}")
            ->greeting("<p class='center-text text-size-20 typography-greeting-text'>{$titleNotification}</p>")
            ->line("<b>Archivo</b>: {$importProcessesRecord->name_file}")
            ->line("<b>Num. Total de registros</b>: {$importProcessesRecord->total_number_of_records}")
            ->line("<b>Num. de registros exitososos</b>: {$importProcessesRecord->total_number_successful_records}")
            ->line("<b>Num. de registros fallidos</b>: {$importProcessesRecord->total_number_failed_records}")
            ->salutation("Firma");
    }

    /*public function toBroadcast ($notifiable) {
        Cache::store('redis')->tags('topic')->flush();
        Cache::store('redis')->tags('import_process')->flush();

        return new BroadcastMessage([]);
    }*/
}
