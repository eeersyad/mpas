{{-- Borang pengguna gaya Settings (telefon). Mengawal medan sebenar dalam borang desktop. $pengguna = null untuk daftar --}}
<div class="d-md-none tetapan-apl">
    @if ($pengguna)
        <div class="kepala-profil-apl">
            <span class="avatar-apl besar">{{ strtoupper(substr($pengguna->usr_name, 0, 2)) }}</span>

            <div class="min-w-0">
                <strong class="d-block text-truncate">{{ $pengguna->usr_name }}</strong>
                <span class="lencana-peranan-apl">{{ $pengguna->usr_level }} · {{ $namaPeranan[$pengguna->usr_level] ?? $pengguna->usr_level }}</span>
                <small class="status-profil-apl">
                    <span class="{{ $pengguna->usr_status === 'ACTIVE' ? 'titik-status-aktif' : 'titik-status-tidak-aktif' }}"></span>
                    {{ $pengguna->usr_status === 'ACTIVE' ? 'Akaun aktif' : 'Akaun tidak aktif' }}
                </small>
            </div>
        </div>
    @endif

    <p class="label-kumpulan-apl">Akaun</p>

    <div class="kad-senarai-apl">
        <button type="button" @class(['baris-tetapan-medan', 'ada-ralat' => $errors->has('usr_name')]) data-sunting-medan="usr_name" data-label="Nama Pengguna">
            <span class="label-medan-apl">Nama pengguna</span>
            <strong data-nilai-medan="usr_name" data-kosong="Belum diisi">-</strong>
            <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
        </button>

        <button type="button" @class(['baris-tetapan-medan', 'ada-ralat' => $errors->has('usr_level')]) data-bs-toggle="offcanvas" data-bs-target="#lembaranPeranan" aria-controls="lembaranPeranan">
            <span class="label-medan-apl">Peranan</span>
            <strong data-nilai-medan="usr_level" data-kosong="Pilih peranan">-</strong>
            <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
        </button>

        <label @class(['baris-tetapan-medan', 'mb-0', 'ada-ralat' => $errors->has('usr_status')]) for="suisStatusApl">
            <span class="label-medan-apl">Akaun aktif</span>
            <span class="form-check form-switch sel-suis-apl">
                @if ($pengguna)
                    <input class="form-check-input" type="checkbox" role="switch" id="suisStatusApl" data-proksi-suis="suisStatusAkaun">
                @else
                    <input class="form-check-input" type="checkbox" role="switch" id="suisStatusApl" data-suis-select="usr_status">
                @endif
            </span>
        </label>
    </div>

    @foreach (['usr_name', 'usr_level', 'usr_status'] as $medanRalat)
        @error($medanRalat)
            <p class="ralat-kumpulan-apl">{{ $message }}</p>
        @enderror
    @endforeach

    <p class="nota-kumpulan-apl">Peranan dan status akaun menentukan akses pengguna dalam sistem.</p>

    <p class="label-kumpulan-apl">Keselamatan</p>

    <div class="kad-senarai-apl">
        <button type="button" @class(['baris-tetapan-medan', 'ada-ralat' => $errors->has('usr_password')]) data-bs-toggle="offcanvas" data-bs-target="#lembaranKataLaluan" aria-controls="lembaranKataLaluan">
            <span class="label-medan-apl">{{ $pengguna ? 'Tukar kata laluan' : 'Kata laluan' }}</span>
            <strong data-status-kata-laluan data-kosong="{{ $pengguna ? 'Tidak ditukar' : 'Belum ditetapkan' }}" data-ada="{{ $pengguna ? 'Akan ditukar ✓' : 'Ditetapkan ✓' }}">-</strong>
            <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
        </button>
    </div>

    @error('usr_password')
        <p class="ralat-kumpulan-apl">{{ $message }}</p>
    @enderror

    <p class="nota-kumpulan-apl">
        {{ $pengguna ? 'Biarkan jika kata laluan pengguna tidak perlu ditukar.' : 'Min. 8 aksara, mengandungi huruf besar, huruf kecil dan nombor.' }}
    </p>
</div>
