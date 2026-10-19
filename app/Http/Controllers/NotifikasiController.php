<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pengguna = $request->user();

        $notifikasi = $pengguna->notifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($notifikasi) => [
                'id' => $notifikasi->id,
                'tajuk' => $notifikasi->data['tajuk'] ?? 'Notifikasi MPAS',
                'mesej' => $notifikasi->data['mesej'] ?? '',
                'ikon' => $notifikasi->data['ikon'] ?? 'bi-bell-fill',
                'url' => $notifikasi->data['url'] ?? null,
                'jenis' => $notifikasi->data['jenis'] ?? 'umum',
                'dibaca' => ! is_null($notifikasi->read_at),
                'masa' => $notifikasi->created_at?->diffForHumans(),
            ]);

        return response()->json([
            'jumlah_belum_baca' => $pengguna->unreadNotifications()->count(),
            'notifikasi' => $notifikasi,
        ]);
    }

    public function baca(Request $request, string $notifikasi): JsonResponse
    {
        $rekod = $request->user()
            ->notifications()
            ->where('id', $notifikasi)
            ->firstOrFail();

        $rekod->markAsRead();

        return response()->json([
            'berjaya' => true,
        ]);
    }

    public function bacaSemua(Request $request): JsonResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'berjaya' => true,
        ]);
    }
}