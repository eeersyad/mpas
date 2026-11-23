<?php

namespace App\Services;

use App\Models\Bangunan;
use App\Models\KomponenPenyelenggaraan;
use App\Models\Penyelenggaraan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Pemeriksaan
{
    public const FOLDER_SEMENTARA = 'sementara/pemeriksaan';

    public const KUNCI_SESI = 'gambar_pemeriksaan_sementara';

    /**
     * Komponen aktif yang ada sekurang-kurangnya satu item aktif.
     */
    public function komponenAktif(): Collection
    {
        return KomponenPenyelenggaraan::query()
            ->aktif()
            ->get()
            ->filter(fn (KomponenPenyelenggaraan $komponen) => count($komponen->itemAktif()) > 0)
            ->values();
    }

    /**
     * Simpan pemeriksaan mengikut mod:
     * TIADA     → tiada tindakan
     * BARU      → cipta rekod baru
     * KEMASKINI → kemas kini rekod terkini (cipta baru jika belum ada)
     */
    public function simpan(Bangunan $bangunan, array $data, int $userId): ?Penyelenggaraan
    {
        $mod = $data['mod'] ?? 'TIADA';

        if ($mod === 'TIADA') {
            return null;
        }

        $rekod = $mod === 'KEMASKINI'
            ? $bangunan->penyelenggaraanTerkini()->first()
            : null;

        $items = $this->binaItems(
            $data['nilai'] ?? [],
            $rekod?->asm_items ?? []
        );

        if ($rekod) {
            $rekod->update([
                'asm_inspection_date' => $data['asm_inspection_date'],
                'asm_items' => $items,
                'asm_note' => $data['asm_note'] ?? null,
                'updated_by' => $userId,
            ]);

            $this->simpanGambar($rekod, $data, $userId);

            return $rekod;
        }

        $rekod = $bangunan->penyelenggaraan()->create([
            'asm_inspection_date' => $data['asm_inspection_date'],
            'asm_inspected_by' => $userId,
            'asm_items' => $items,
            'asm_note' => $data['asm_note'] ?? null,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $this->simpanGambar($rekod, $data, $userId);

        return $rekod;
    }

    /**
     * Pautkan gambar sementara (dimuat naik dari borang) kepada rekod,
     * dan padam gambar sedia ada yang ditanda (mod KEMASKINI sahaja).
     */
    private function simpanGambar(Penyelenggaraan $rekod, array $data, int $userId): void
    {
        $disk = Storage::disk('local');

        if (($data['mod'] ?? null) === 'KEMASKINI' && ! empty($data['padam_gambar'])) {
            $rekod->gambar()
                ->whereIn('asmi_id', array_map('intval', (array) $data['padam_gambar']))
                ->get()
                ->each(function ($gambar) use ($disk): void {
                    $disk->delete($gambar->asmi_path);
                    $gambar->delete();
                });
        }

        $tokenSah = session(self::KUNCI_SESI, []);

        foreach ($data['gambar'] ?? [] as $baris) {
            $token = $baris['token'] ?? '';
            $sementara = self::FOLDER_SEMENTARA . '/' . $token;

            if (! in_array($token, $tokenSah, true) || ! $disk->exists($sementara)) {
                continue;
            }

            $laluan = 'pemeriksaan/' . $rekod->asm_id . '/' . $token;
            $disk->move($sementara, $laluan);

            $rekod->gambar()->create([
                'asmi_name' => trim($baris['nama']),
                'asmi_summary' => filled($baris['ringkasan'] ?? null) ? trim($baris['ringkasan']) : null,
                'asmi_path' => $laluan,
                'asmi_mime' => $disk->mimeType($laluan) ?: null,
                'asmi_size' => $disk->size($laluan),
                'uploaded_by' => $userId,
            ]);

            $tokenSah = array_values(array_diff($tokenSah, [$token]));
        }

        session([self::KUNCI_SESI => $tokenSah]);
    }

    /**
     * Bina JSON asm_items:
     * [{"amc_id":1,"nama":"...","item":[{"id":"k1i1","nama":"...","nilai":"BAIK"}]}]
     *
     * Item atau komponen dalam rekod lama yang kini tidak aktif dikekalkan.
     */
    private function binaItems(array $nilai, array $sediaAda): array
    {
        $lama = collect($sediaAda)->keyBy('amc_id');
        $hasil = [];

        foreach ($this->komponenAktif() as $komponen) {
            $itemLama = collect($lama->get($komponen->amc_id)['item'] ?? [])
                ->keyBy('id');

            $senarai = [];

            foreach ($komponen->itemAktif() as $item) {
                $senarai[$item['id']] = [
                    'id' => $item['id'],
                    'nama' => $item['nama'],
                    'nilai' => $nilai[$komponen->amc_id][$item['id']] ?? null,
                ];
            }

            foreach ($itemLama as $id => $item) {
                if (! isset($senarai[$id])) {
                    $senarai[$id] = $item;
                }
            }

            $hasil[$komponen->amc_id] = [
                'amc_id' => $komponen->amc_id,
                'nama' => $komponen->amc_name,
                'item' => array_values($senarai),
            ];

            $lama->forget($komponen->amc_id);
        }

        foreach ($lama as $amcId => $komponen) {
            $hasil[$amcId] = $komponen;
        }

        return array_values($hasil);
    }
}