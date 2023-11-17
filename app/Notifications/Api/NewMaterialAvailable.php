<?php

namespace App\Notifications\Api;

use App\Models\Lesson;
use App\Models\Material;
use App\Models\Opposition;
use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMaterialAvailable extends Notification implements ShouldQueue
{
    use Queueable;

    private Lesson $lesson;
    private Material $material;

    /**
     * @param Lesson $lesson
     * @param Material $material
     */
    public function __construct(Lesson $lesson, Material $material)
    {

        $this->lesson = $lesson;
        $this->material = $material;

    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $date = date('d/m/Y', strtotime($this->lesson->date));

        return (new MailMessage)
            ->subject("Clase {$this->lesson->name} - Nuevo Material")
            ->greeting("<p class='center-text text-size-20 typography-greeting-text'>{$this->lesson->name}</p>")
            ->line(" Se ha incorporado el material {$this->material->name} a la clase {$this->lesson->name} del día {$date}.")
            ->line('Podrás consultarlo desde  “Mis Clases" o en el apartado "Materiales".')
            ->line("¡A darle caña!")
            ->salutation("Firma");
    }

}
