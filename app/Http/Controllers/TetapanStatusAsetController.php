<?php

namespace App\Http\Controllers;

use App\Models\StatusAset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Tetapan > Status Aset: status premis (bangunan) dan status tanah.
 * Tiada hapus: guna Aktif / Tidak aktif. Kod dijana sekali dan dikunci.
 */
class TetapanStatusAsetController extends Controller
{
    public function index(Request $request): View
    {
        $jenis = $request->query('jenis') === 'TANAH' ? 'TANAH' : 'BANGUNAN';

        $status = StatusAset::query()
            ->jenis($jenis)
            ->tersusun()
            ->get();

        // Bilangan rekod yang menggunakan setiap status
        $penggunaan = $jenis === 'BANGUNAN'
            ? \App\Models\Bangunan::query()->selectRaw('asb_premise_status as kod, COUNT(*) as jumlah')->groupBy('asb_premise_status')->pluck('jumlah', 'kod')
            : \App\Models\Tanah::query()->selectRaw('asl_status_tanah as kod, COUNT(*) as jumlah')->groupBy('asl_status_tanah')->pluck('jumlah', 'kod');

        return view('tetapan.status-aset', compact('jenis', 'status', 'penggunaan'));
    }

    public function simpan(Request $request): RedirectResponse
    {
        $request->merge(['sts_jenis' => $request->input('sts_jenis') === 'TANAH' ? 'TANAH' : 'BANGUNAN']);
        $jenis = $request->input('sts_jenis');

        $data = $request->validate(
            [
                'sts_jenis' => ['required', Rule::in(array_keys(StatusAset::JENIS))],
                ...$this->peraturan($request, $jenis),
            ],
            $this->mesej()
        );

        $status = StatusAset::create([
            ...$data,
            'sts_kod' => $this->janaKod($data['sts_label'], $jenis),
            'sts_boleh_iklan' => $jenis === 'BANGUNAN' && $request->boolean('sts_boleh_iklan'),
            'sts_susunan' => (int) StatusAset::query()->jenis($jenis)->max('sts_susunan') + 1,
            'created_by' => $request->user()->usr_id,
            'updated_by' => $request->user()->usr_id,
        ]);

        return redirect()
            ->route('tetapan.status-aset', ['jenis' => $jenis])
            ->with('berjaya', "Status {$status->sts_label} berjaya ditambah.");
    }

    public function kemaskini(Request $request, StatusAset $status): RedirectResponse
    {
        $data = $request->validate($this->peraturan($request, $status->sts_jenis, $status), $this->mesej());

        $status->update([
            ...$data,
            'sts_boleh_iklan' => $status->sts_jenis === 'BANGUNAN' && $request->boolean('sts_boleh_iklan'),
            'updated_by' => $request->user()->usr_id,
        ]);

        return redirect()
            ->route('tetapan.status-aset', ['jenis' => $status->sts_jenis])
            ->with('berjaya', "Status {$status->sts_label} berjaya dikemaskini.");
    }

    /**
     * Tukar status aktif / tidak aktif terus dari senarai (AJAX).
     */
    public function togolStatus(Request $request, StatusAset $status): JsonResponse
    {
        $data = $request->validate(['aktif' => ['required', 'boolean']]);
        $nilai = $data['aktif'] ? 'ACTIVE' : 'INACTIVE';

        $status->update([
            'sts_status' => $nilai,
            'updated_by' => $request->user()->usr_id,
        ]);

        return response()->json([
            'status' => $nilai,
            'label' => \App\Support\Paparan::label($nilai),
            'mesej' => $data['aktif'] ? "{$status->sts_label} diaktifkan." : "{$status->sts_label} dinyahaktifkan.",
        ]);
    }

    private function peraturan(Request $request, string $jenis, ?StatusAset $status = null): array
    {
        if ($request->input('sts_warna') === 'suai') {
            $request->merge(['sts_warna' => strtoupper((string) $request->input('sts_warna_suai'))]);
        }

        $request->merge([
            'sts_label' => $this->formatTajuk($request->input('sts_label')),
            'sts_status' => $request->boolean('sts_status') ? 'ACTIVE' : 'INACTIVE',
        ]);

        return [
            'sts_label' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tetapan_status_aset', 'sts_label')
                    ->where('sts_jenis', $jenis)
                    ->ignore($status?->sts_id, 'sts_id'),
            ],
            'sts_warna' => ['required', function ($atribut, $nilai, $gagal) {
                if (! array_key_exists($nilai, StatusAset::WARNA) && ! StatusAset::adalahHex($nilai)) {
                    $gagal('Warna yang dipilih tidak sah.');
                }
            }],
            'sts_status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }

    private function mesej(): array
    {
        return [
            'sts_label.required' => 'Sila masukkan nama status.',
            'sts_label.unique' => 'Status dengan nama ini telah wujud.',
            'sts_warna.required' => 'Sila pilih warna.',
            'sts_warna.in' => 'Warna yang dipilih tidak sah.',
        ];
    }

    /**
     * Kod daripada label, contoh "Kosong Sedia Disewa" → KOSONG_SEDIA_DISEWA (unik bagi setiap jenis).
     */
    private function janaKod(string $label, string $jenis): string
    {
        $asas = Str::limit(Str::upper(Str::slug($label, '_')), 45, '') ?: 'STATUS';
        $kod = $asas;
        $i = 2;

        while (StatusAset::query()->jenis($jenis)->where('sts_kod', $kod)->exists()) {
            $kod = $asas . '_' . $i++;
        }

        return $kod;
    }

    private function formatTajuk(mixed $nilai): ?string
    {
        if (! is_string($nilai) || trim($nilai) === '') {
            return null;
        }

        // Kekalkan singkatan huruf besar (cth. MDM)
        return collect(preg_split('/\s+/', trim($nilai)))
            ->map(fn ($kata) => mb_strtoupper($kata) === $kata && mb_strlen($kata) <= 3 ? $kata : mb_convert_case(mb_strtolower($kata), MB_CASE_TITLE, 'UTF-8'))
            ->implode(' ');
    }
}
