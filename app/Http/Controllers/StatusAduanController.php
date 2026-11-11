<?php

namespace App\Http\Controllers;

use App\Models\Aduan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StatusAduanController extends Controller
{
    public function borang(): View
    {
        return view('aduan.status-borang');
    }

    public function cari(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'rujukan' => ['required', 'string', 'max:20'],
                'emel' => ['required', 'email:rfc', 'max:150'],
            ],
            [
                'rujukan.required' => 'Sila masukkan no. rujukan aduan.',
                'emel.required' => 'Sila masukkan alamat emel yang digunakan semasa membuat aduan.',
                'emel.email' => 'Sila masukkan alamat emel yang sah.',
            ]
        );

        $aduan = Aduan::query()
            ->where('cmp_ref_no', Str::upper(trim($data['rujukan'])))
            ->where('cmp_reporter_email', Str::lower(trim($data['emel'])))
            ->first();

        if (! $aduan) {
            return back()
                ->withInput()
                ->withErrors(['rujukan' => 'Aduan tidak dijumpai. Sila semak no. rujukan dan alamat emel.']);
        }

        return redirect()->to($aduan->urlStatus());
    }

    public function papar(string $rujukan): View
    {
        $aduan = Aduan::query()
            ->with('bangunan:asb_id,asb_name')
            ->where('cmp_ref_no', $rujukan)
            ->firstOrFail();

        return view('aduan.status', compact('aduan'));
    }
}
