<?php

namespace App\Http\Controllers;

use App\Models\Bangunan;
use App\Models\DokumenBangunan;
use App\Models\DokumenTanah;
use App\Models\Tanah;
use App\Support\Paparan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Portal Awam: senarai aset bangunan & tanah yang tersedia untuk disewa,
 * peta lokasi dan navigasi. Tiada log masuk.
 */
class PortalAwamController extends Controller
{
    private const FORMAT_GAMBAR = ['jpg', 'jpeg', 'png', 'webp'];

    public function index(): View
    {
        return view('portal.utama', [
            'bilTersedia' => $this->queryBangunan()->count() + $this->queryTanah()->count(),
            'hubungan' => config('portal.hubungan'),
        ]);
    }

    public function sewa(Request $request): View
    {
        $jenis = in_array($request->query('jenis'), ['bangunan', 'tanah'], true) ? $request->query('jenis') : null;
        $cari = trim((string) $request->query('cari'));
        $mukim = mb_strtoupper(trim((string) $request->query('mukim')));

        $bangunan = $jenis === 'tanah' ? collect() : $this->queryBangunan()
            ->when($cari !== '', fn (Builder $q) => $q->where(fn (Builder $q) => $q
                ->where('asb_name', 'like', "%{$cari}%")
                ->orWhere('asb_add1', 'like', "%{$cari}%")
                ->orWhere('asb_add2', 'like', "%{$cari}%")
                ->orWhere('asb_city', 'like', "%{$cari}%")
                ->orWhere('asb_category', 'like', "%{$cari}%")))
            ->when($mukim !== '', fn (Builder $q) => $q->whereRaw('UPPER(TRIM(asb_mukim)) = ?', [$mukim]))
            ->with(['dokumen' => fn ($q) => $q->where('asbdoc_type', 'GAMBAR')->orderBy('asbdoc_id')])
            ->orderBy('asb_name')
            ->get()
            ->map(fn (Bangunan $b) => $this->dataBangunan($b));

        $tanah = $jenis === 'bangunan' ? collect() : $this->queryTanah()
            ->when($cari !== '', fn (Builder $q) => $q->where(fn (Builder $q) => $q
                ->where('asl_lot_no', 'like', "%{$cari}%")
                ->orWhere('asl_location', 'like', "%{$cari}%")))
            ->when($mukim !== '', fn (Builder $q) => $q->whereRaw('UPPER(TRIM(asl_mukim)) = ?', [$mukim]))
            ->with(['dokumen' => fn ($q) => $q->where('asldoc_type', 'GAMBAR')->orderBy('asldoc_id')])
            ->orderBy('asl_location')
            ->get()
            ->map(fn (Tanah $t) => $this->dataTanah($t));

        $aset = $bangunan->concat($tanah)->values();

        return view('portal.sewa', [
            'aset' => $aset,
            'jenis' => $jenis,
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

    public function tanah(Tanah $tanah): View
    {
        abort_unless($this->queryTanah()->whereKey($tanah->getKey())->exists(), 404);

        $tanah->load(['dokumen' => fn ($q) => $q->where('asldoc_type', 'GAMBAR')->orderBy('asldoc_id')]);

        return view('portal.butiran', [
            'aset' => $this->dataTanah($tanah),
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
            'description' => 'Aset bangunan dan tanah Majlis Daerah Marang yang tersedia untuk disewa',
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
        } elseif ($jenis === 'tanah') {
            $rekod = DokumenTanah::query()->findOrFail($dokumen);
            abort_unless($rekod->asldoc_type === 'GAMBAR', 404);
            abort_unless($this->queryTanah()->whereKey($rekod->asl_id)->exists(), 404);
            $path = $rekod->asldoc_path;
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
        return Bangunan::query()
            ->whereIn('asb_premise_status', config('portal.status_tersedia'))
            ->where(fn (Builder $q) => $q
                ->whereNull('asb_maintenance_status')
                ->orWhereNotIn('asb_maintenance_status', config('portal.penyelenggaraan_dikecualikan')));
    }

    private function queryTanah(): Builder
    {
        return Tanah::query()->whereIn('asl_status_tanah', config('portal.status_tersedia'));
    }

    private function dataBangunan(Bangunan $b): array
    {
        $alamat = collect([$b->asb_add1, $b->asb_add2, trim($b->asb_postcode . ' ' . $b->asb_city)])
            ->map(fn ($baris) => trim((string) $baris))
            ->filter()
            ->implode(', ');

        return $this->lengkapkan([
            'jenis' => 'bangunan',
            'jenis_label' => 'Bangunan',
            'id' => $b->asb_id,
            'tajuk' => $b->asb_name,
            'subtajuk' => collect([$b->asb_category, $b->asb_sub_category])->filter()->map(fn ($k) => Paparan::label($k))->implode(' · '),
            'status' => Paparan::label($b->asb_premise_status),
            'alamat' => $alamat ?: null,
            'mukim' => $b->asb_mukim ? Paparan::label($b->asb_mukim) : null,
            'keluasan' => $b->asb_size,
            'lat' => $b->asb_latitude,
            'lng' => $b->asb_longitude,
            'gambar' => $b->dokumen
                ->filter(fn ($d) => in_array(strtolower(pathinfo((string) $d->asbdoc_path, PATHINFO_EXTENSION)), self::FORMAT_GAMBAR, true))
                ->map(fn ($d) => $d->asbdoc_id)
                ->values(),
            'url' => route('portal.sewa.bangunan', $b),
        ]);
    }

    private function dataTanah(Tanah $t): array
    {
        return $this->lengkapkan([
            'jenis' => 'tanah',
            'jenis_label' => 'Tanah',
            'id' => $t->asl_id,
            'tajuk' => trim(($t->asl_location ?: 'Tanah') . ($t->asl_lot_no ? ' (Lot ' . $t->asl_lot_no . ')' : '')),
            'subtajuk' => $t->asl_lot_no ? 'Lot ' . $t->asl_lot_no : null,
            'status' => Paparan::label($t->asl_status_tanah),
            'alamat' => $t->asl_location,
            'mukim' => $t->asl_mukim ? Paparan::label($t->asl_mukim) : null,
            'keluasan' => $t->asl_land_size,
            'lat' => $t->asl_latitude,
            'lng' => $t->asl_longitude,
            'gambar' => $t->dokumen
                ->filter(fn ($d) => in_array(strtolower(pathinfo((string) $d->asldoc_path, PATHINFO_EXTENSION)), self::FORMAT_GAMBAR, true))
                ->map(fn ($d) => $d->asldoc_id)
                ->values(),
            'url' => route('portal.sewa.tanah', $t),
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
            ->concat($this->queryTanah()->whereNotNull('asl_mukim')->distinct()->pluck('asl_mukim'))
            ->map(fn ($m) => mb_strtoupper(trim((string) $m)))
            ->filter()
            ->unique()
            ->sort()
            ->mapWithKeys(fn ($m) => [$m => Paparan::label($m)]);
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
