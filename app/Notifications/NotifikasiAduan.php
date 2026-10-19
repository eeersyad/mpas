<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NotifikasiAduan extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $tajuk,
        private readonly string $mesej,
        private readonly string $ikon,
        private readonly string $url,
        private readonly string $jenis,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tajuk' => $this->tajuk,
            'mesej' => $this->mesej,
            'ikon' => $this->ikon,
            'url' => $this->url,
            'jenis' => $this->jenis,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}