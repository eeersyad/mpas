<?php

namespace App\Http\Controllers;

use App\Models\Aduan;
use App\Models\Bangunan;
use App\Models\Tanah;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $ringkasan = match ($user->usr_level) {
            'SYSTEM', 'BPH' => [
                'bangunan' => Bangunan::count(),
                'tanah' => Tanah::count(),
                'aduan' => Aduan::whereIn('cmp_status', [
                    'BARU',
                    'DITERIMA',
                    'DALAM_PENYELENGGARAAN',
                ])->count(),
                'pengguna' => User::where('usr_status', 'ACTIVE')->count(),
            ],

            'BP' => [
                'tanah' => Tanah::count(),
            ],

            'KJ' => [
                'aduan' => Aduan::whereIn('cmp_status', [
                    'DITERIMA',
                    'DALAM_PENYELENGGARAAN',
                ])->count(),
            ],

            default => [],
        };

        return view('dashboard', compact('user', 'ringkasan'));
    }
}