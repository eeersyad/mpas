<?php

namespace App\Services;

use App\Models\Aduan;
use App\Models\User;
use App\Notifications\NotifikasiAduan as NotifikasiAduanRekod;
use App\Notifications\StatusAduanPengadu;
use Illuminate\Support\Facades\Notification;

class NotifikasiAduan
{
    /**
     * Hantar emel status kepada pengadu (DITERIMA_SISTEM, SELESAI atau DITOLAK).
     */
    public function maklumkanPengadu(Aduan $aduan, string $jenis): void
    {
        if (blank($aduan->cmp_reporter_email) || blank($aduan->cmp_ref_no)) {
            return;
        }

        Notification::route('mail', $aduan->cmp_reporter_email)
            ->notify(new StatusAduanPengadu($aduan, $jenis));
    }

    public function aduanBaharu(Aduan $aduan): void
    {
        $this->hantar(
            ['SYSTEM', 'BPH'],
            $aduan,
            'Aduan baharu diterima',
            'Aduan baharu bagi '.$this->namaBangunan($aduan).' memerlukan semakan.',
            'bi-clipboard2-plus-fill',
            'aduan_baharu'
        );
    }

    public function aduanDiterima(Aduan $aduan): void
    {
        $this->hantar(
            ['SYSTEM', 'KJ'],
            $aduan,
            'Aduan telah diterima',
            'Aduan bagi '.$this->namaBangunan($aduan).' telah diterima untuk tindakan penyelenggaraan.',
            'bi-clipboard2-check-fill',
            'aduan_diterima'
        );
    }

    public function aduanDikemaskini(Aduan $aduan, string $status): void
    {
        $selesai = $status === 'SELESAI';

        $this->hantar(
            ['SYSTEM', 'BPH'],
            $aduan,
            $selesai ? 'Aduan telah diselesaikan' : 'Status aduan dikemaskini',
            $selesai
                ? 'Aduan bagi '.$this->namaBangunan($aduan).' telah ditandakan selesai.'
                : 'Tindakan penyelenggaraan bagi '.$this->namaBangunan($aduan).' telah dikemaskini.',
            $selesai ? 'bi-check-circle-fill' : 'bi-tools',
            $selesai ? 'aduan_selesai' : 'aduan_kemaskini'
        );
    }

    private function hantar(
        array $peranan,
        Aduan $aduan,
        string $tajuk,
        string $mesej,
        string $ikon,
        string $jenis
    ): void {
        $penerima = User::query()
            ->whereIn('usr_level', $peranan)
            ->where('usr_status', 'ACTIVE')
            ->get();

        if ($penerima->isEmpty()) {
            return;
        }

        Notification::send(
            $penerima,
            new NotifikasiAduanRekod(
                $tajuk,
                $mesej,
                $ikon,
                route('aduan.show', $aduan),
                $jenis
            )
        );
    }

    private function namaBangunan(Aduan $aduan): string
    {
        $aduan->loadMissing('bangunan');

        return $aduan->bangunan?->asb_name ?? 'aset bangunan';
    }
}