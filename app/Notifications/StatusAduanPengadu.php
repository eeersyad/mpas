<?php

namespace App\Notifications;

use App\Models\Aduan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusAduanPengadu extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * $jenis: DITERIMA_SISTEM, SELESAI atau DITOLAK
     */
    public function __construct(
        public Aduan $aduan,
        public string $jenis,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $aduan = $this->aduan->loadMissing('bangunan');
        $rujukan = $aduan->cmp_ref_no;
        $bangunan = $aduan->bangunan?->asb_name ?? '-';

        $mesej = (new MailMessage)
            ->greeting('Assalamualaikum dan salam sejahtera,');

        if ($this->jenis === 'DITERIMA_SISTEM') {
            $mesej->subject("[MPAS] Aduan {$rujukan} telah diterima")
                ->line('Terima kasih kerana menghantar aduan kepada Majlis Daerah Marang. Aduan anda telah diterima dan akan disemak oleh Bahagian Pengurusan Harta.')
                ->line("**No. rujukan:** {$rujukan}")
                ->line("**Aset:** {$bangunan}")
                ->line("**Jenis kerosakan:** {$aduan->cmp_damage_type}")
                ->line('Simpan no. rujukan ini untuk menyemak status aduan anda.');
        }

        if ($this->jenis === 'SELESAI') {
            $mesej->subject("[MPAS] Aduan {$rujukan} telah selesai")
                ->line('Aduan anda telah diselesaikan oleh Majlis Daerah Marang.')
                ->line("**No. rujukan:** {$rujukan}")
                ->line("**Aset:** {$bangunan}")
                ->line('**Catatan:** ' . ($aduan->cmp_completion_note ?: '-'));
        }

        if ($this->jenis === 'DITOLAK') {
            $mesej->subject("[MPAS] Aduan {$rujukan} tidak dapat diteruskan")
                ->line('Harap maaf, aduan anda tidak dapat diteruskan untuk tindakan.')
                ->line("**No. rujukan:** {$rujukan}")
                ->line("**Aset:** {$bangunan}")
                ->line('**Sebab:** ' . ($aduan->sebabDitolak() ?: '-'));
        }

        return $mesej
            ->action('Semak Status Aduan', $aduan->urlStatus())
            ->line('Emel ini dijana secara automatik. Sila jangan balas emel ini.')
            ->salutation("Sekian, terima kasih.\n\nMajlis Daerah Marang");
    }
}
