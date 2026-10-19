<aside class="tetapan-sisi tetapan-sisi-moden">
    <div class="kepala-menu-tetapan">
        <span class="ikon-menu-tetapan">
            <i class="bi bi-sliders2-vertical"></i>
        </span>

        <div>
            <p>PUSAT KONFIGURASI</p>
            <strong>Tetapan Sistem</strong>
        </div>
    </div>

    <nav class="nav flex-column menu-tetapan-moden">
        <a
            href="{{ route('tetapan.rujukan') }}"
            class="tetapan-pautan {{ request()->routeIs('tetapan.rujukan') ? 'aktif' : '' }}"
        >
            <i class="bi bi-upc-scan"></i>

            <span>
                <strong>Nombor Rujukan</strong>
                <small>Format aset tanah dan bangunan</small>
            </span>
        </a>

        <a
            href="{{ route('tetapan.aduan') }}"
            class="tetapan-pautan {{ request()->routeIs('tetapan.aduan') ? 'aktif' : '' }}"
        >
            <i class="bi bi-clipboard2-pulse"></i>

            <span>
                <strong>Aduan Awam</strong>
                <small>Paparan aduan melalui QR</small>
            </span>
        </a>

        <div class="pemisah-menu-tetapan"></div>

        <span class="tetapan-pautan tidak-aktif">
            <i class="bi bi-buildings"></i>

            <span>
                <strong>Maklumat Organisasi</strong>
                <small>Akan datang</small>
            </span>
        </span>

        <span class="tetapan-pautan tidak-aktif">
            <i class="bi bi-image"></i>

            <span>
                <strong>Logo Sistem</strong>
                <small>Akan datang</small>
            </span>
        </span>

        <span class="tetapan-pautan tidak-aktif">
            <i class="bi bi-palette-fill"></i>

            <span>
                <strong>Paparan Sistem</strong>
                <small>Akan datang</small>
            </span>
        </span>
    </nav>
</aside>