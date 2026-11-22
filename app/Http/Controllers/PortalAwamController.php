<?php

namespace App\Http\Controllers;

use App\Models\Bangunan;
use App\Models\StatusAset;
use App\Models\DokumenBangunan;
use App\Support\Paparan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Portal Awam: senarai aset bangunan yang tersedia untuk disewa,
 * peta lokasi dan navigasi. Tiada log masuk.
 */
class PortalAwamController extends Controller
{
    private const FORMAT_GAMBAR = ['jpg', 'jpeg', 'png', 'webp'];

    public function index(): View
    {
        return view('portal.utama', [
            'bilTersedia' => $this->queryBangunan()->count(),
            'hubungan' => config('portal.hubungan'),
        ]);
    }

    public function sewa(Request $request): View
    {
        $cari = trim((string) $request->query('cari'));
        $mukim = mb_strtoupper(trim((string) $request->query('mukim')));
        $kategori = mb_strtoupper(trim((string) $request->query('kategori')));

        $aset = $this->queryBangunan()
            ->when($cari !== '', fn (Builder $q) => $q->where(fn (Builder $q) => $q
                ->where('asb_name', 'like', "%{$cari}%")
                ->orWhere('asb_add1', 'like', "%{$cari}%")
                ->orWhere('asb_add2', 'like', "%{$cari}%")
                ->orWhere('asb_city', 'like', "%{$cari}%")
                ->orWhere('asb_category', 'like', "%{$cari}%")
                ->orWhere('asb_sub_category', 'like', "%{$cari}%")))
            ->when($mukim !== '', fn (Builder $q) => $q->whereRaw('UPPER(TRIM(asb_mukim)) = ?', [$mukim]))
            ->when($kategori !== '', fn (Builder $q) => $q->whereRaw('UPPER(TRIM(asb_category)) = ?', [$kategori]))
            ->with(['dokumen' => fn ($q) => $q->where('asbdoc_type', 'GAMBAR')->orderBy('asbdoc_id')])
            ->orderBy('asb_name')
            ->get()
            ->map(fn (Bangunan $b) => $this->dataBangunan($b))
            ->values();

        return view('portal.sewa', [
            'aset' => $aset,
            'kategori' => $kategori,
            'senaraiKategori' => $this->senaraiKategori(),
            'cari' => $cari,
            'mukim' => $mukim,
            'senaraiMukim' => $this->senaraiMukim(),
            'paparan' => $request->query('paparan') === 'peta' ? 'peta' : 'senarai',
            'hubungan' => config('portal.hubungan'),
        ]);
    }

    public function bangunan(Bangunan $bangunan): View
    {
        abort_unless($this->queryBangunan()->whereKey($bangunan->getKey())->exists(), 404);

        $bangunan->load(['dokumen' => fn ($q) => $q->where('asbdoc_type', 'GAMBAR')->orderBy('asbdoc_id')]);

        return view('portal.butiran', [
            'aset' => $this->dataBangunan($bangunan),
            'hubungan' => config('portal.hubungan'),
        ]);
    }

    /**
     * Manifest PWA portal awam (boleh "Tambah ke Skrin Utama").
     */
    public function manifest(): Response
    {
        $data = [
            'name' => 'MPAS Portal Aset Awam',
            'short_name' => 'Aset MDM',
            'description' => 'Aset bangunan Majlis Daerah Marang yang tersedia untuk disewa',
            'lang' => 'ms',
            'start_url' => '/awam',
            'scope' => '/awam',
            'display' => 'standalone',
            'background_color' => '#f4f8f7',
            'theme_color' => '#168a7e',
            'icons' => [
                ['src' => asset('images/pwa-192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => asset('images/pwa-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
        ];

        return response(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 200, [
            'Content-Type' => 'application/manifest+json',
        ]);
    }

    /**
     * Service worker ringkas: rangkaian dahulu, cache sebagai sandaran bila offline.
     */
    public function serviceWorker(): Response
    {
        $js = <<<'JS'
const CACHE = 'mpas-awam-v1';
self.addEventListener('install', (e) => self.skipWaiting());
self.addEventListener('activate', (e) => e.waitUntil(
    caches.keys().then((k) => Promise.all(k.filter((n) => n !== CACHE).map((n) => caches.delete(n)))).then(() => self.clients.claim())
));
self.addEventListener('fetch', (e) => {
    const req = e.request;
    if (req.method !== 'GET' || new URL(req.url).origin !== location.origin) return;
    e.respondWith(
        fetch(req).then((res) => {
            if (res.ok) { const salin = res.clone(); caches.open(CACHE).then((c) => c.put(req, salin)); }
            return res;
        }).catch(() => caches.match(req))
    );
});
JS;

        return response($js, 200, [
            'Content-Type' => 'application/javascript',
            'Service-Worker-Allowed' => '/awam',
            'Cache-Control' => 'no-cache',
        ]);
    }

    /**
     * Gambar aset untuk awam: hanya dokumen jenis GAMBAR daripada aset yang tersedia.
     * Gambar dikecilkan dan disimpan dalam cache supaya pantas.
     */
    public function gambar(string $jenis, int $dokumen, string $saiz = 'kecil'): Response
    {
        $lebarMaks = $saiz === 'besar' ? 1600 : 640;

        if ($jenis === 'bangunan') {
            $rekod = DokumenBangunan::query()->findOrFail($dokumen);
            abort_unless($rekod->asbdoc_type === 'GAMBAR', 404);
            abort_unless($this->queryBangunan()->whereKey($rekod->asb_id)->exists(), 404);
            $path = $rekod->asbdoc_path;
        } else {
            abort(404);
        }

        abort_unless(in_array(strtolower(pathinfo((string) $path, PATHINFO_EXTENSION)), self::FORMAT_GAMBAR, true), 404);

        $asal = $this->laluanFail($path);
        abort_unless($asal, 404);

        $cache = "portal-cache/{$jenis}-{$dokumen}-{$lebarMaks}-" . filemtime($asal) . '.jpg';

        if (! Storage::disk('local')->exists($cache)) {
            $jpeg = $this->kecilkanGambar($asal, $lebarMaks);
            abort_unless($jpeg !== null, 404);
            Storage::disk('local')->put($cache, $jpeg);
        }

        return response(Storage::disk('local')->get($cache), 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    // ------------------------------------------------------------------

    private function queryBangunan(): Builder
    {
        // Hanya bangunan dengan status premis "Boleh diiklankan" (Tetapan > Status Aset) yang ditanda untuk diiklankan
        return Bangunan::query()
            ->whereIn('asb_premise_status', StatusAset::kodBolehIklan())
            ->where('asb_advertise', true)
            ->where(fn (Builder $q) => $q->whereNull('asb_advertise_start')->orWhereDate('asb_advertise_start', '<=', today()))
            ->where(fn (Builder $q) => $q->whereNull('asb_advertise_end')->orWhereDate('asb_advertise_end', '>=', today()));
    }

    private function dataBangunan(Bangunan $b): array
    {
        $alamat = collect([$b->asb_add1, $b->asb_add2, trim($b->asb_postcode . ' ' . $b->asb_city)])
            ->map(fn ($baris) => trim((string) $baris))
            ->filter()
            ->implode(', ');

        return $this->lengkapkan([
            'jenis' => 'bangunan',
            'jenis_label' => $b->asb_category ? Paparan::label($b->asb_category) : 'Bangunan',
            'id' => $b->asb_id,
            'tajuk' => $b->asb_name,
            'subtajuk' => collect([$b->asb_category, $b->asb_sub_category])->filter()->map(fn ($k) => Paparan::label($k))->implode(' · '),
            'status' => StatusAset::label('BANGUNAN', $b->asb_premise_status),
            'alamat' => $alamat ?: null,
            'mukim' => $b->asb_mukim ? Paparan::label($b->asb_mukim) : null,
            'keluasan' => $b->asb_size,
            'lat' => $b->asb_latitude,
            'lng' => $b->asb_longitude,
            'gambar' => $b->dokumen
                ->filter(fn ($d) => in_array(strtolower(pathinfo((string) $d->asbdoc_path, PATHINFO_EXTENSION)), self::FORMAT_GAMBAR, true))
                ->map(fn ($d) => $d->asbdoc_id)
                ->values(),
            'iklan_mula' => $b->asb_advertise_start?->format('d/m/Y'),
            'iklan_tamat' => $b->asb_advertise_end?->format('d/m/Y'),
            'url' => route('portal.sewa.bangunan', $b),
        ]);
    }

    private function lengkapkan(array $data): array
    {
        $lat = is_numeric($data['lat']) ? (float) $data['lat'] : null;
        $lng = is_numeric($data['lng']) ? (float) $data['lng'] : null;
        $adaLokasi = $lat !== null && $lng !== null && ! ($lat == 0.0 && $lng == 0.0);

        $data['lat'] = $adaLokasi ? $lat : null;
        $data['lng'] = $adaLokasi ? $lng : null;
        $data['google_maps'] = $adaLokasi ? "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lng}" : null;
        $data['waze'] = $adaLokasi ? "https://waze.com/ul?ll={$lat},{$lng}&navigate=yes" : null;
        $data['keluasan_teks'] = is_numeric($data['keluasan']) ? number_format((float) $data['keluasan'], 2) . ' m²' : null;
        $data['gambar_kecil'] = $data['gambar']->map(fn ($id) => route('portal.gambar', [$data['jenis'], $id, 'kecil']))->all();
        $data['gambar_besar'] = $data['gambar']->map(fn ($id) => route('portal.gambar', [$data['jenis'], $id, 'besar']))->all();
        unset($data['gambar']);

        return $data;
    }

    private function senaraiMukim(): Collection
    {
        return $this->queryBangunan()->whereNotNull('asb_mukim')->distinct()->pluck('asb_mukim')
            ->map(fn ($m) => mb_strtoupper(trim((string) $m)))
            ->filter()
            ->unique()
            ->sort()
            ->mapWithKeys(fn ($m) => [$m => Paparan::label($m)]);
    }

    private function senaraiKategori(): Collection
    {
        return $this->queryBangunan()->whereNotNull('asb_category')->distinct()->pluck('asb_category')
            ->map(fn ($k) => mb_strtoupper(trim((string) $k)))
            ->filter()
            ->unique()
            ->sort()
            ->mapWithKeys(fn ($k) => [$k => Paparan::label($k)]);
    }

    private function laluanFail(?string $path): ?string
    {
        foreach (['local', 'public'] as $disk) {
            if ($path && Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->path($path);
            }
        }

        return null;
    }

    private function kecilkanGambar(string $laluan, int $lebarMaks): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $gambar = @imagecreatefromstring((string) file_get_contents($laluan));

        if (! $gambar) {
            return null;
        }

        $lebar = imagesx($gambar);
        $tinggi = imagesy($gambar);
        $skala = min(1, $lebarMaks / max($lebar, $tinggi));
        $lebarBaru = max(1, (int) round($lebar * $skala));
        $tinggiBaru = max(1, (int) round($tinggi * $skala));

        $kanvas = imagecreatetruecolor($lebarBaru, $tinggiBaru);
        imagefill($kanvas, 0, 0, imagecolorallocate($kanvas, 255, 255, 255));
        imagecopyresampled($kanvas, $gambar, 0, 0, 0, 0, $lebarBaru, $tinggiBaru, $lebar, $tinggi);

        ob_start();
        imagejpeg($kanvas, null, 80);
        $data = ob_get_clean();

        imagedestroy($gambar);
        imagedestroy($kanvas);

        return $data ?: null;
    }
}
