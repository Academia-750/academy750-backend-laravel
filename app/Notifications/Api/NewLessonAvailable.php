<?php

namespace App\Notifications\Api;

use App\Models\Lesson;
use App\Models\Opposition;
use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLessonAvailable extends Notification implements ShouldQueue
{
    use Queueable;

    private Lesson $lesson;

    /**
     * @param Lesson $lesson
     */
    public function __construct(Lesson $lesson)
    {

        $this->lesson = $lesson;

    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $date = date('Y-m-d', $this->lesson->date);

        return (new MailMessage)
            ->subject("Clase  {$this->lesson->name} disponible")
            ->greeting("<p class='center-text text-size-20 typography-greeting-text'>{$this->lesson->name}</p>")
            ->line("Hola! La clase  {$this->lesson->name} del día {$date} ya se encuentra activada.")
            ->line('Podrás consultar los archivos vinculados a la misma desde tu área "Mis Clases". Así mismo, en tu apartado "Materiales", aparecerán los nuevos documentos.')
            ->line("¡A darle caña!")
            ->salutation("Firma");
    }

}