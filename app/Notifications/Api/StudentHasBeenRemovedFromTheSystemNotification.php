<?php

namespace App\Notifications\Api;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class StudentHasBeenRemovedFromTheSystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        try {
            $dni = $notifiable->dni;

            DB::beginTransaction();

            $user = User::query()->find($notifiable->id);

            $user?->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

        return (new MailMessage)
            ->subject("Baja al alumno {$dni}")
            ->greeting("<span class='greeting-text-default-mailable typography-greeting-text text-size-18'>Academia 750 - Solicitud de baja de alumno</span>")
            ->line('Dar de baja al alumno con el número de documento:')
            ->line($dni)
            ->salutation("Firma");
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
