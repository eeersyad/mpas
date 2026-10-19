<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Jadual;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengurusanPenggunaController extends Controller
{
    public function index(Request $request): View
    {
        $had = Jadual::had($request);

        $cari = trim((string) $request->input('cari', ''));
        $peranan = $request->input('peranan');
        $status = $request->input('status');

        $pengguna = User::query()
            ->whereIn('usr_level', $this->perananDibenarkan())
            ->when(
                $cari !== '',
                fn ($query) => $query->where('usr_name', 'like', "%{$cari}%")
            )
            ->when(
                $peranan,
                fn ($query, $nilai) => $query->where('usr_level', $nilai)
            )
            ->when(
                $status,
                fn ($query, $nilai) => $query->where('usr_status', $nilai)
            )
            ->orderBy('usr_level')
            ->orderBy('usr_name')
            ->paginate($had)
            ->withQueryString();

        return view('pengguna.senarai', compact('pengguna'));
    }

    public function create(): View
    {
        $peranan = $this->perananDibenarkan();

        return view('pengguna.daftar', compact('peranan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $peranan = $this->perananDibenarkan();

        $data = $request->validate(
            [
                'usr_name' => ['required', 'string', 'max:100', 'unique:users,usr_name'],
                'usr_password' => ['required', 'string', 'min:8', 'confirmed'],
                'usr_level' => ['required', 'in:'.implode(',', $peranan)],
                'usr_status' => ['required', 'in:ACTIVE,INACTIVE'],
            ],
            [
                'usr_name.required' => 'Sila masukkan nama pengguna.',
                'usr_name.unique' => 'Nama pengguna ini telah digunakan.',
                'usr_password.required' => 'Sila masukkan kata laluan.',
                'usr_password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
                'usr_password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
                'usr_level.required' => 'Sila pilih peranan pengguna.',
                'usr_level.in' => 'Anda tidak dibenarkan mencipta peranan ini.',
                'usr_status.required' => 'Sila pilih status pengguna.',
            ]
        );

        User::create([
            'usr_name' => $data['usr_name'],
            'usr_password' => $data['usr_password'],
            'usr_level' => $data['usr_level'],
            'usr_status' => $data['usr_status'],
            'created_by' => $request->user()->usr_id,
        ]);

        return redirect()
            ->route('pengguna.index')
            ->with('berjaya', 'Pengguna baharu berjaya didaftarkan.');
    }

    public function edit(User $pengguna): View
    {
        $this->pastikanBolehKemaskini($pengguna);

        $peranan = $this->perananDibenarkan();

        return view('pengguna.kemaskini', compact('pengguna', 'peranan'));
    }

    public function update(Request $request, User $pengguna): RedirectResponse
    {
        $this->pastikanBolehKemaskini($pengguna);

        $peranan = $this->perananDibenarkan();

        $data = $request->validate(
            [
                'usr_name' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('users', 'usr_name')->ignore($pengguna->usr_id, 'usr_id'),
                ],
                'usr_password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'usr_level' => ['required', 'in:'.implode(',', $peranan)],
                'usr_status' => ['required', 'in:ACTIVE,INACTIVE'],
            ],
            [
                'usr_name.required' => 'Sila masukkan nama pengguna.',
                'usr_name.unique' => 'Nama pengguna ini telah digunakan.',
                'usr_password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
                'usr_password.confirmed' => 'Pengesahan kata laluan tidak sepadan.',
                'usr_level.required' => 'Sila pilih peranan pengguna.',
                'usr_level.in' => 'Anda tidak dibenarkan menukar kepada peranan ini.',
                'usr_status.required' => 'Sila pilih status pengguna.',
            ]
        );

        $maklumat = [
            'usr_name' => $data['usr_name'],
            'usr_level' => $data['usr_level'],
            'usr_status' => $data['usr_status'],
            'updated_by' => $request->user()->usr_id,
        ];

        if (! empty($data['usr_password'])) {
            $maklumat['usr_password'] = $data['usr_password'];
        }

        $pengguna->update($maklumat);

        return redirect()
            ->route('pengguna.index')
            ->with('berjaya', 'Maklumat pengguna berjaya dikemaskini.');
    }

    public function destroy(Request $request, User $pengguna): RedirectResponse
    {
        $this->pastikanBolehKemaskini($pengguna);

        if ($pengguna->usr_id === $request->user()->usr_id) {
            return redirect()
                ->route('pengguna.index')
                ->with('ralat', 'Anda tidak boleh memadam akaun sendiri.');
        }

        $pengguna->delete();

        return redirect()
            ->route('pengguna.index')
            ->with('berjaya', 'Pengguna berjaya dipadam secara kekal.');
    }

    private function perananDibenarkan(): array
    {
        return request()->user()->usr_level === 'SYSTEM'
            ? ['BPH', 'BP', 'KJ']
            : ['BP', 'KJ'];
    }

    private function pastikanBolehKemaskini(User $pengguna): void
    {
        abort_unless(
            in_array($pengguna->usr_level, $this->perananDibenarkan(), true),
            403
        );
    }
}