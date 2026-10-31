<?php

namespace App\Http\Controllers;

use App\Models\KomponenPenyelenggaraan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TetapanKomponenController extends Controller
{
    public function index(): View
    {
        $komponen = KomponenPenyelenggaraan::query()
            ->orderBy('amc_order')
            ->orderBy('amc_id')
            ->get();

        return view('tetapan.komponen', compact('komponen'));
    }

    public function simpan(Request $request): RedirectResponse
    {
        $data = $this->sahkan($request);
        $userId = $request->user()->usr_id;

        DB::transaction(function () use ($data, $userId): void {
            // Cipta dahulu untuk dapatkan amc_id (digunakan dalam ID item)
            $komponen = KomponenPenyelenggaraan::create([
                'amc_name' => trim($data['amc_name']),
                'amc_items' => [],
                'amc_order' => $data['amc_order'],
                'amc_status' => $data['amc_status'],
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $komponen->update([
                'amc_items' => $this->binaItem($komponen, $data['items']),
            ]);
        });

        return redirect()
            ->route('tetapan.komponen')
            ->with('berjaya', 'Komponen penyelenggaraan berjaya ditambah.');
    }

    public function kemaskini(
        Request $request,
        KomponenPenyelenggaraan $komponen
    ): RedirectResponse {
        $data = $this->sahkan($request, $komponen);

        $komponen->update([
            'amc_name' => trim($data['amc_name']),
            'amc_items' => $this->binaItem($komponen, $data['items']),
            'amc_order' => $data['amc_order'],
            'amc_status' => $data['amc_status'],
            'updated_by' => $request->user()->usr_id,
        ]);

        return redirect()
            ->route('tetapan.komponen')
            ->with('berjaya', "Komponen {$komponen->amc_name} berjaya dikemaskini.");
    }

    private function sahkan(
        Request $request,
        ?KomponenPenyelenggaraan $komponen = null
    ): array {
        return $request->validate(
            [
                'amc_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('asb_maintenance_component', 'amc_name')
                        ->ignore($komponen?->amc_id, 'amc_id'),
                ],
                'amc_order' => ['required', 'integer', 'min:1', 'max:999'],
                'amc_status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],

                'items' => ['required', 'array', 'min:1'],
                'items.*.id' => ['nullable', 'string', 'max:50'],
                'items.*.nama' => ['required', 'string', 'max:255'],
                'items.*.turutan' => ['required', 'integer', 'min:1', 'max:999'],
                'items.*.status' => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
            ],
            [
                'amc_name.required' => 'Sila masukkan nama komponen.',
                'amc_name.unique' => 'Nama komponen ini telah wujud.',
                'amc_order.required' => 'Sila masukkan turutan komponen.',
                'amc_order.integer' => 'Turutan mesti dalam bentuk nombor.',
                'items.required' => 'Komponen mesti mempunyai sekurang-kurangnya satu item.',
                'items.min' => 'Komponen mesti mempunyai sekurang-kurangnya satu item.',
                'items.*.nama.required' => 'Sila lengkapkan nama bagi setiap item.',
                'items.*.turutan.required' => 'Sila masukkan turutan bagi setiap item.',
                'items.*.turutan.integer' => 'Turutan item mesti dalam bentuk nombor.',
                'in' => 'Status yang dipilih tidak sah.',
            ]
        );
    }

    /**
     * Bina semula senarai item JSON.
     * - Item sedia ada kekal dengan ID yang sama.
     * - Item baru diberi ID automatik.
     * - Item sedia ada yang tiada dalam borang tidak dibuang, hanya dinyahaktifkan.
     */
    private function binaItem(KomponenPenyelenggaraan $komponen, array $input): array
    {
        $sediaAda = collect($komponen->amc_items ?? [])->keyBy('id');
        $idDigunakan = $sediaAda->keys()->all();
        $hasil = [];

        $inputTersusun = collect($input)
            ->sortBy(fn ($item) => (int) $item['turutan'])
            ->values();

        foreach ($inputTersusun as $item) {
            $id = $item['id'] ?? null;

            if (! $id || ! $sediaAda->has($id)) {
                $id = $this->janaIdItem($komponen->amc_id, $idDigunakan);
                $idDigunakan[] = $id;
            }

            $hasil[$id] = [
                'id' => $id,
                'nama' => trim($item['nama']),
                'status' => $item['status'],
            ];
        }

        foreach ($sediaAda as $id => $item) {
            if (! isset($hasil[$id])) {
                $hasil[$id] = array_merge($item, ['status' => 'INACTIVE']);
            }
        }

        return array_values($hasil);
    }

    /**
     * Jana ID item unik dalam komponen, contoh: k4i1, k4i2, ...
     */
    private function janaIdItem(int $amcId, array $idDigunakan): string
    {
        $nombor = 1;

        do {
            $id = "k{$amcId}i{$nombor}";
            $nombor++;
        } while (in_array($id, $idDigunakan, true));

        return $id;
    }
}