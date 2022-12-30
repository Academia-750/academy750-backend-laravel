<?php

namespace App\Notifications\Api;

use App\Models\Opposition;
use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimQuestionNotification extends Notification/* implements ShouldQueue*/
{
    use Queueable;

    private Opposition $opposition;
    private Question $question;
    private Topic $topic;
    private string $claim_text;
    private User $user;

    /**
     * @param Opposition $opposition
     * @param Question $question
     * @param Topic $topic
     * @param User $user
     * @param string $claim_text
     */
    public function __construct(Opposition $opposition, Question $question, Topic $topic, string $claim_text, User $user)
    {
        //
        $this->opposition = $opposition;
        $this->question = $question;
        $this->topic = $topic;
        $this->claim_text = $claim_text;
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(" {$this->opposition->name}_{$this->topic->name}")
            ->greeting("<p class='center-text text-size-20 typography-greeting-text'>¡Solicitud de impugnación!</p>")
            ->line("<b>Pregunta</b>: {$this->question->question}")
            ->line("<b>Motivo</b>: {$this->claim_text}")
            ->line("<b>Nombre del Alumno</b>: {$this->user->full_name}")
            ->line("<b>Correo del Alumno</b>: {$this->user->email}")
            ->salutation("Firma");
    }

}
