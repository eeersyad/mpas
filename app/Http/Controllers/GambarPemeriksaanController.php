<?php

namespace App\Http\Controllers;

use App\Models\GambarPemeriksaan;
use App\Models\Penyelenggaraan;
use App\Models\TetapanPenyelenggaraan;
use App\Services\Pemeriksaan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GambarPemeriksaanController extends Controller
{
    /**
     * Muat naik sementara dari borang (satu gambar setiap permintaan).
     * Gambar hanya dipautkan kepada pemeriksaan apabila borang aset disimpan.
     */
    public function simpanSementara(Request $request): JsonResponse
    {
        abort_unless(TetapanPenyelenggaraan::bolehIsi($request->user()), 403);

        $request->validate([
            'gambar' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'gambar.required' => 'Sila pilih gambar.',
            'gambar.uploaded' => 'Gambar gagal dimuat naik. Saiz fail mungkin terlalu besar.',
            'gambar.image' => 'Fail mestilah gambar.',
            'gambar.mimes' => 'Gambar mestilah dalam format JPG, PNG atau WEBP.',
            'gambar.max' => 'Saiz gambar tidak boleh melebihi 10MB.',
        ]);

        $fail = $request->file('gambar');
        $token = Str::uuid() . '.' . strtolower($fail->extension() ?: 'jpg');

        $fail->storeAs(Pemeriksaan::FOLDER_SEMENTARA, $token, 'local');

        $request->session()->push(Pemeriksaan::KUNCI_SESI, $token);

        return response()->json([
            'token' => $token,
            'url' => route('pemeriksaan.gambar.sementara', $token),
        ]);
    }

    public function lihatSementara(Request $request, string $token): StreamedResponse
    {
        abort_unless(in_array($token, $request->session()->get(Pemeriksaan::KUNCI_SESI, []), true), 404);

        $laluan = Pemeriksaan::FOLDER_SEMENTARA . '/' . $token;

        abort_unless(Storage::disk('local')->exists($laluan), 404);

        return Storage::disk('local')->response($laluan);
    }

    public function lihat(Request $request, Penyelenggaraan $pemeriksaan, GambarPemeriksaan $gambar): StreamedResponse
    {
        $this->sahkanAkses($request, $pemeriksaan, $gambar);

        return Storage::disk('local')->response($gambar->asmi_path, $gambar->namaFailMuatTurun(), [
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    public function muatTurun(Request $request, Penyelenggaraan $pemeriksaan, GambarPemeriksaan $gambar): StreamedResponse
    {
        $this->sahkanAkses($request, $pemeriksaan, $gambar);

        return Storage::disk('local')->download($gambar->asmi_path, $gambar->namaFailMuatTurun());
    }

    private function sahkanAkses(Request $request, Penyelenggaraan $pemeriksaan, GambarPemeriksaan $gambar): void
    {
        abort_unless(TetapanPenyelenggaraan::bolehLihat($request->user()), 403);
        abort_unless($gambar->asm_id === $pemeriksaan->asm_id, 404);
        abort_unless(Storage::disk('local')->exists($gambar->asmi_path), 404);
    }
}
