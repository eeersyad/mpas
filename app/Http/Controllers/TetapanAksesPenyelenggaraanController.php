<?php

namespace App\Http\Controllers;

use App\Models\AksesPenyelenggaraan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Tetapan > Akses Penyelenggaraan: senarai pengguna yang boleh lihat / isi
 * rekod pemeriksaan penyelenggaraan. SYSTEM sentiasa ada akses penuh.
 */
class TetapanAksesPenyelenggaraanController extends Controller
{
    public function index(): View
    {
        $akses = AksesPenyelenggaraan::query()
            ->with('pengguna:usr_id,usr_name,usr_level,usr_status')
            ->whereHas('pengguna')
            ->get()
            ->sortBy([
                fn ($a, $b) => $b->aktif() <=> $a->aktif(),
                fn ($a, $b) => strcasecmp($a->pengguna->usr_name, $b->pengguna->usr_name),
            ])
            ->values();

        // Pengguna yang boleh ditambah: aktif, bukan SYSTEM, belum ada dalam senarai
        $penggunaBoleh = User::query()
            ->where('usr_status', 'ACTIVE')
            ->where('usr_level', '!=', 'SYSTEM')
            ->whereNotIn('usr_id', $akses->pluck('usr_id'))
            ->orderBy('usr_name')
            ->get(['usr_id', 'usr_name', 'usr_level']);

        return view('tetapan.penyelenggaraan', compact('akses', 'penggunaBoleh'));
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'usr_id' => [
                    'required',
                    Rule::exists('users', 'usr_id')->where('usr_status', 'ACTIVE')->whereNot('usr_level', 'SYSTEM'),
                    Rule::unique('tetapan_akses_penyelenggaraan', 'usr_id'),
                ],
                ...$this->peraturanAkses($request),
            ],
            [
                'usr_id.required' => 'Sila pilih pengguna.',
                'usr_id.exists' => 'Pengguna yang dipilih tidak sah.',
                'usr_id.unique' => 'Pengguna ini sudah ada dalam senarai akses.',
                ...$this->mesejAkses(),
            ]
        );

        $akses = AksesPenyelenggaraan::create([
            ...$data,
            'created_by' => $request->user()->usr_id,
            'updated_by' => $request->user()->usr_id,
        ]);

        return redirect()
            ->route('tetapan.penyelenggaraan')
            ->with('berjaya', "Akses untuk {$akses->pengguna?->usr_name} berjaya ditambah.");
    }

    public function kemaskini(Request $request, AksesPenyelenggaraan $akses): RedirectResponse
    {
        $data = $request->validate($this->peraturanAkses($request), $this->mesejAkses());

        $akses->update([
            ...$data,
            'updated_by' => $request->user()->usr_id,
        ]);

        return redirect()
            ->route('tetapan.penyelenggaraan')
            ->with('berjaya', "Akses untuk {$akses->pengguna?->usr_name} berjaya dikemaskini.");
    }

    /**
     * Tukar status aktif / tidak aktif terus dari senarai (AJAX).
     */
    public function togolStatus(Request $request, AksesPenyelenggaraan $akses): JsonResponse
    {
        $data = $request->validate(['aktif' => ['required', 'boolean']]);
        $status = $data['aktif'] ? 'ACTIVE' : 'INACTIVE';

        $akses->update([
            'tap_status' => $status,
            'updated_by' => $request->user()->usr_id,
        ]);

        $nama = $akses->pengguna?->usr_name;

        return response()->json([
            'status' => $status,
            'label' => \App\Support\Paparan::label($status),
            'mesej' => $data['aktif'] ? "Akses {$nama} diaktifkan." : "Akses {$nama} dinyahaktifkan.",
        ]);
    }

    private function peraturanAkses(Request $request): array
    {
        $request->merge([
            'tap_status' => $request->boolean('tap_status') ? 'ACTIVE' : 'INACTIVE',
        ]);

        return [
            'tap_tahap' => ['required', Rule::in(array_keys(AksesPenyelenggaraan::SENARAI_TAHAP))],
            'tap_status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }

    private function mesejAkses(): array
    {
        return [
            'tap_tahap.required' => 'Sila pilih tahap akses.',
            'tap_tahap.in' => 'Tahap akses yang dipilih tidak sah.',
        ];
    }
}
