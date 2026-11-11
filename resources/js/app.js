import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

window.L = L;

import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    */
    const akar = document.documentElement;
    const toggleSidebar = document.querySelector('#toggleSidebar');

    if (toggleSidebar) {
        let senaraiTooltip = [];

        // Tooltip hanya aktif ketika sidebar mini
        const pasangTooltip = (mini) => {
            senaraiTooltip.forEach((tooltip) => tooltip.dispose());
            senaraiTooltip = [];

            if (! mini || ! window.bootstrap) {
                return;
            }

            document.querySelectorAll('.sidebar [data-tooltip-sidebar]').forEach((elemen) => {
                senaraiTooltip.push(
                    new window.bootstrap.Tooltip(elemen, {
                        title: elemen.dataset.tooltipSidebar,
                        placement: 'right',
                        trigger: 'hover focus',
                        container: 'body',
                        customClass: 'tooltip-sidebar',
                        offset: [0, 14],
                    })
                );
            });
        };

        const kemaskiniButang = (mini) => {
            const label = mini ? 'Besarkan sidebar' : 'Kecilkan sidebar';

            toggleSidebar.setAttribute('aria-expanded', String(! mini));
            toggleSidebar.setAttribute('aria-label', label);
            toggleSidebar.setAttribute('title', label);
        };

        const miniAwal = akar.classList.contains('sidebar-mini');

        kemaskiniButang(miniAwal);
        pasangTooltip(miniAwal);

        // Hidupkan semula animasi selepas halaman siap dilukis
        requestAnimationFrame(() => {
            requestAnimationFrame(() => akar.classList.remove('sidebar-memuat'));
        });

        toggleSidebar.addEventListener('click', () => {
            const mini = akar.classList.toggle('sidebar-mini');

            kemaskiniButang(mini);
            pasangTooltip(mini);

            try {
                localStorage.setItem('mpas_sidebar', mini ? 'mini' : 'besar');
            } catch (e) {}
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Menu telefon
    |--------------------------------------------------------------------------
    */
    const toggleMenuTelefon = document.querySelector('#toggleMenuTelefon');
    const menuTelefon = document.querySelector('#menuTelefon');
    const tutupMenuTelefon = document.querySelector('[data-tutup-menu-telefon]');

    if (toggleMenuTelefon && menuTelefon) {
        const latarMenuTelefon = document.createElement('div');
        latarMenuTelefon.className = 'latar-menu-telefon';

        document.body.appendChild(latarMenuTelefon);

        const bukaMenuTelefon = () => {
            menuTelefon.classList.add('terbuka');
            latarMenuTelefon.classList.add('aktif');
            document.body.classList.add('menu-telefon-terbuka');

            menuTelefon.setAttribute('aria-hidden', 'false');
            toggleMenuTelefon.setAttribute('aria-expanded', 'true');

            tutupMenuTelefon?.focus();
        };

        const tutupMenu = () => {
            menuTelefon.classList.remove('terbuka');
            latarMenuTelefon.classList.remove('aktif');
            document.body.classList.remove('menu-telefon-terbuka');

            menuTelefon.setAttribute('aria-hidden', 'true');
            toggleMenuTelefon.setAttribute('aria-expanded', 'false');

            toggleMenuTelefon.focus();
        };

        toggleMenuTelefon.addEventListener('click', bukaMenuTelefon);
        tutupMenuTelefon?.addEventListener('click', tutupMenu);
        latarMenuTelefon.addEventListener('click', tutupMenu);

        menuTelefon
            .querySelectorAll('[data-pautan-menu-telefon]')
            .forEach((pautan) => {
                pautan.addEventListener('click', tutupMenu);
            });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && menuTelefon.classList.contains('terbuka')) {
                tutupMenu();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Notifikasi
    |--------------------------------------------------------------------------
    */
    const panelNotifikasi = document.querySelector('[data-panel-notifikasi]');
    const senaraiNotifikasi = document.querySelector('[data-senarai-notifikasi]');
    const lencanaNotifikasi = document.querySelector('[data-lencana-notifikasi]');
    const tokenCsrf = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (panelNotifikasi && senaraiNotifikasi && aplikasi) {
        const kemaskiniLencana = (jumlah) => {
            const jumlahNombor = Number(jumlah) || 0;
            const adaNotifikasi = jumlahNombor > 0;

            lencanaNotifikasi.hidden = !adaNotifikasi;
            lencanaNotifikasi.textContent = jumlahNombor > 9 ? '9+' : jumlahNombor;

            lencanaNotifikasi.setAttribute(
                'aria-label',
                adaNotifikasi
                    ? `${jumlahNombor} notifikasi belum dibaca`
                    : 'Tiada notifikasi belum dibaca'
            );
        };

        const paparkanKosong = (mesej = 'Tiada notifikasi buat masa ini.') => {
            senaraiNotifikasi.replaceChildren();

            const ruang = document.createElement('div');
            ruang.className = 'notifikasi-kosong';
            ruang.innerHTML = '<i class="bi bi-bell-slash"></i>';

            const teks = document.createElement('p');
            teks.textContent = mesej;

            ruang.appendChild(teks);
            senaraiNotifikasi.appendChild(ruang);
        };

        const binaItemNotifikasi = (notifikasi, sisipkanDiAtas = false) => {
            const butang = document.createElement('button');
            butang.type = 'button';
            butang.className = `item-notifikasi${notifikasi.dibaca ? '' : ' belum-dibaca'}`;
            butang.dataset.id = notifikasi.id;
            butang.dataset.url = notifikasi.url || '';

            const ikon = document.createElement('span');
            ikon.className = 'ikon-notifikasi';
            ikon.innerHTML = `<i class="bi ${notifikasi.ikon || 'bi-bell-fill'}"></i>`;

            const kandungan = document.createElement('span');
            kandungan.className = 'kandungan-notifikasi';

            const tajuk = document.createElement('strong');
            tajuk.textContent = notifikasi.tajuk || 'Notifikasi MPAS';

            const mesej = document.createElement('p');
            mesej.textContent = notifikasi.mesej || '';

            const masa = document.createElement('small');
            masa.textContent = notifikasi.masa || 'Sekarang';

            kandungan.append(tajuk, mesej, masa);
            butang.append(ikon, kandungan);

            if (!notifikasi.dibaca) {
                const titik = document.createElement('span');
                titik.className = 'titik-belum-baca';
                butang.appendChild(titik);
            }

            if (sisipkanDiAtas) {
                senaraiNotifikasi.prepend(butang);
            } else {
                senaraiNotifikasi.appendChild(butang);
            }
        };

        const muatkanNotifikasi = async () => {
            try {
                const respons = await fetch(panelNotifikasi.dataset.urlSenarai, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!respons.ok) {
                    throw new Error('Notifikasi tidak dapat dimuatkan.');
                }

                const data = await respons.json();

                senaraiNotifikasi.replaceChildren();

                if (!data.notifikasi?.length) {
                    paparkanKosong();
                } else {
                    data.notifikasi.forEach((notifikasi) => {
                        binaItemNotifikasi(notifikasi);
                    });
                }

                kemaskiniLencana(data.jumlah_belum_baca);
            } catch {
                paparkanKosong('Notifikasi tidak dapat dimuatkan.');
            }
        };

        const tandakanDibaca = async (id) => {
            await fetch(`${panelNotifikasi.dataset.urlBaca}/${id}/baca`, {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': tokenCsrf,
                },
            });
        };

        senaraiNotifikasi.addEventListener('click', async (event) => {
            const item = event.target.closest('[data-id]');

            if (!item) {
                return;
            }

            if (item.classList.contains('belum-dibaca')) {
                await tandakanDibaca(item.dataset.id);

                item.classList.remove('belum-dibaca');
                item.querySelector('.titik-belum-baca')?.remove();

                kemaskiniLencana(
                    senaraiNotifikasi.querySelectorAll(
                        '.item-notifikasi.belum-dibaca'
                    ).length
                );
            }

            if (item.dataset.url) {
                window.location.href = item.dataset.url;
            }
        });

        document.querySelector('[data-baca-semua]')?.addEventListener('click', async () => {
            await fetch(panelNotifikasi.dataset.urlBacaSemua, {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': tokenCsrf,
                },
            });

            senaraiNotifikasi.querySelectorAll('.item-notifikasi').forEach((item) => {
                item.classList.remove('belum-dibaca');
                item.querySelector('.titik-belum-baca')?.remove();
            });

            kemaskiniLencana(0);
        });

        panelNotifikasi.closest('.dropdown')?.addEventListener(
            'show.bs.dropdown',
            muatkanNotifikasi
        );

        const userId = aplikasi.dataset.userId;

        if (window.Echo && userId) {
            window.Echo
                .private(`App.Models.User.${userId}`)
                .notification((notifikasi) => {
                    let maklumat = notifikasi.data ?? notifikasi;

                    if (typeof maklumat === 'string') {
                        maklumat = JSON.parse(maklumat);
                    }

                    const data = {
                        ...maklumat,
                        id: notifikasi.id ?? maklumat.id,
                        dibaca: false,
                        masa: 'Sekarang',
                    };

                    if (senaraiNotifikasi.querySelector('.notifikasi-kosong')) {
                        senaraiNotifikasi.replaceChildren();
                    }

                    binaItemNotifikasi(data, true);

                    kemaskiniLencana(
                        senaraiNotifikasi.querySelectorAll(
                            '.item-notifikasi.belum-dibaca'
                        ).length
                    );

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: data.tajuk || 'Notifikasi baharu',
                        text: data.mesej || 'Terdapat kemas kini baharu dalam sistem MPAS.',
                        showConfirmButton: false,
                        timer: 4500,
                        timerProgressBar: true,
                    });
                });
        }

        muatkanNotifikasi();
    }

    /*
    |--------------------------------------------------------------------------
    | Padam pengguna
    |--------------------------------------------------------------------------
    */
    document.querySelectorAll('[data-padam-pengguna]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const namaPengguna = form.dataset.namaPengguna;

            Swal.fire({
                icon: 'warning',
                title: 'Padam pengguna?',
                html: `Pengguna <strong>${namaPengguna}</strong> akan dipadam secara kekal dan tidak boleh dipulihkan.`,
                showCancelButton: true,
                confirmButtonText: 'Ya, padam',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Borang aset tanah
    |--------------------------------------------------------------------------
    */
    const borangTanah = document.querySelector('[data-borang-tanah]');

    if (borangTanah) {
        const panel = [...borangTanah.querySelectorAll('[data-langkah]')];
        const penunjuk = [...borangTanah.querySelectorAll('[data-penunjuk-item]')];
        const kemajuan = borangTanah.querySelector('[data-kemajuan-tanah]');
        let langkahSemasa = 0;

        const kemaskiniPaparan = () => {
            penunjuk.forEach((item, indeks) => {
                item.classList.toggle('aktif', indeks === langkahSemasa);
                item.classList.toggle('selesai', indeks < langkahSemasa);
            });

            if (kemajuan && panel.length > 1) {
                kemajuan.style.width = `${(langkahSemasa / (panel.length - 1)) * 100}%`;
            }
        };

        const tukarLangkah = (sasaran, arah) => {
            if (sasaran < 0 || sasaran >= panel.length || sasaran === langkahSemasa) {
                return;
            }

            const semasa = panel[langkahSemasa];

            semasa.classList.add(
                arah === 'seterusnya' ? 'keluar-kiri' : 'keluar-kanan'
            );

            window.setTimeout(() => {
                semasa.classList.remove('aktif', 'keluar-kiri', 'keluar-kanan');

                langkahSemasa = sasaran;

                panel[langkahSemasa].classList.add('aktif');
                kemaskiniPaparan();
            }, 220);
        };

        borangTanah.querySelectorAll('[data-seterusnya]').forEach((butang) => {
            butang.addEventListener('click', () => {
                const inputWajib = panel[langkahSemasa].querySelectorAll('[required]');

                for (const input of inputWajib) {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        return;
                    }
                }

                tukarLangkah(langkahSemasa + 1, 'seterusnya');
            });
        });

        borangTanah.querySelectorAll('[data-sebelumnya]').forEach((butang) => {
            butang.addEventListener('click', () => {
                tukarLangkah(langkahSemasa - 1, 'sebelumnya');
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Dokumen tanah dinamik
        |--------------------------------------------------------------------------
        */
        const senaraiDokumenTanah = borangTanah.querySelector(
            '[data-senarai-dokumen-tanah]'
        );

        const butangTambahDokumenTanah = borangTanah.querySelector(
            '[data-tambah-dokumen-tanah]'
        );

        const templatDokumenTanah = document.querySelector(
            '#templatDokumenTanah'
        );

        const jumlahDokumenTanahBaharu = () =>
            senaraiDokumenTanah?.querySelectorAll('[data-dokumen-tanah]')
                .length ?? 0;

        const jumlahDokumenTanahSediaAda = Number(
            senaraiDokumenTanah?.dataset.dokumenSediaAda || 0
        );

        const kemaskiniMesejDokumenTanah = () => {
            if (!senaraiDokumenTanah) {
                return;
            }

            const mesej = senaraiDokumenTanah.querySelector(
                '[data-mesej-dokumen-tanah]'
            );

            const jumlahBaharu = jumlahDokumenTanahBaharu();
            const jumlahKeseluruhan =
                jumlahDokumenTanahSediaAda + jumlahBaharu;

            if (jumlahBaharu === 0 && !mesej) {
                senaraiDokumenTanah.insertAdjacentHTML(
                    'beforeend',
                    `
                        <div class="col-12" data-mesej-dokumen-tanah>
                            <div class="kad-kosong py-4">
                                <div class="ilustrasi-kosong">
                                    <i class="bi bi-folder2-open"></i>
                                </div>
                                <h3>Tiada dokumen baharu</h3>
                                <p>Tekan “Tambah Dokumen” jika ada dokumen untuk dimuat naik.</p>
                            </div>
                        </div>
                    `
                );
            }

            if (jumlahBaharu > 0 && mesej) {
                mesej.remove();
            }

            if (butangTambahDokumenTanah) {
                const hadPenuh = jumlahKeseluruhan >= 4;

                butangTambahDokumenTanah.disabled = hadPenuh;
                butangTambahDokumenTanah.title = hadPenuh
                    ? 'Had maksimum 4 dokumen telah dicapai.'
                    : 'Tambah dokumen baharu';
            }
        };

        butangTambahDokumenTanah?.addEventListener('click', () => {
            if (!senaraiDokumenTanah || !templatDokumenTanah) {
                return;
            }

            if (
                jumlahDokumenTanahSediaAda +
                    jumlahDokumenTanahBaharu() >=
                4
            ) {
                Swal.fire({
                    icon: 'info',
                    title: 'Had dokumen dicapai',
                    text: 'Setiap aset tanah hanya boleh mempunyai maksimum 4 dokumen.',
                    confirmButtonColor: '#168A7E',
                });

                return;
            }

            const indeks = Number(
                senaraiDokumenTanah.dataset.indeksSeterusnya || 0
            );

            const kandungan = templatDokumenTanah.innerHTML.replaceAll(
                '__INDEX__',
                indeks
            );

            senaraiDokumenTanah.insertAdjacentHTML(
                'beforeend',
                kandungan
            );

            senaraiDokumenTanah.dataset.indeksSeterusnya = indeks + 1;

            kemaskiniMesejDokumenTanah();
        });

        senaraiDokumenTanah?.addEventListener('click', (event) => {
            const butangBuang = event.target.closest(
                '[data-buang-dokumen-tanah]'
            );

            if (!butangBuang) {
                return;
            }

            const kadDokumen = butangBuang.closest(
                '[data-dokumen-tanah]'
            );

            Swal.fire({
                icon: 'warning',
                title: 'Buang dokumen baharu?',
                text: 'Dokumen ini tidak akan dimuat naik.',
                showCancelButton: true,
                confirmButtonText: 'Ya, buang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
            }).then((hasil) => {
                if (!hasil.isConfirmed || !kadDokumen) {
                    return;
                }

                kadDokumen.style.opacity = '0';
                kadDokumen.style.transform = 'translateY(-8px)';

                window.setTimeout(() => {
                    kadDokumen.remove();
                    kemaskiniMesejDokumenTanah();
                }, 180);
            });
        });

        document
            .querySelectorAll('[data-padam-dokumen-tanah]')
            .forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Padam dokumen?',
                        html: `Dokumen <strong>${form.dataset.namaDokumen}</strong> akan dipadam secara kekal.`,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, padam',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true,
                    }).then((hasil) => {
                        if (hasil.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

        kemaskiniMesejDokumenTanah();

        const sahkanBorangTanah = () => {
        const medanTidakSah = [
            ...borangTanah.querySelectorAll('input, select, textarea'),
        ].find((item) => !item.checkValidity());

        if (!medanTidakSah) {
            return true;
        }

        const panelRalat = medanTidakSah.closest('[data-langkah]');
        const indeksPanel = panel.indexOf(panelRalat);

        if (indeksPanel !== -1 && indeksPanel !== langkahSemasa) {
            tukarLangkah(
                indeksPanel,
                indeksPanel > langkahSemasa ? 'seterusnya' : 'sebelumnya'
            );
        }

        window.setTimeout(() => {
            medanTidakSah.reportValidity();
        }, 250);

        Swal.fire({
            icon: 'warning',
            title: 'Maklumat belum lengkap atau tidak sah',
            text: 'Sila semak ruangan yang ditunjukkan sebelum menyimpan.',
            confirmButtonColor: '#168A7E',
        });

        return false;
    };

    borangTanah.addEventListener('submit', (event) => {
        event.preventDefault();

        if (!sahkanBorangTanah()) {
            return;
        }

        Swal.fire({
            icon: 'question',
            title: 'Simpan aset tanah?',
            text: 'Sistem akan menjana nombor rujukan secara automatik.',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#168A7E',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                HTMLFormElement.prototype.submit.call(borangTanah);
            }
        });
    });

        kemaskiniPaparan();
    }

    /*
    |--------------------------------------------------------------------------
    | Modal butiran aset
    |--------------------------------------------------------------------------
    */
    const modalButiranAset = document.querySelector('#modalButiranAset');
    const aktifkanTabButiran = (bekas) => {
        const butangTab = [
            ...bekas.querySelectorAll('[data-tab-butiran-butang]'),
        ];

        const panelTab = [
            ...bekas.querySelectorAll('[data-tab-butiran-panel]'),
        ];

        if (!butangTab.length || !panelTab.length) {
            return;
        }

        const bukaTab = (nomborTab) => {
            butangTab.forEach((butang) => {
                const aktif = butang.dataset.tabButiranButang === nomborTab;

                butang.classList.toggle('aktif', aktif);
                butang.setAttribute('aria-selected', aktif ? 'true' : 'false');
            });

            panelTab.forEach((panel) => {
                const aktif = panel.dataset.tabButiranPanel === nomborTab;

                panel.classList.toggle('aktif', aktif);
                panel.hidden = !aktif;
            });

            bekas.querySelector('.modal-body')?.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        };

        butangTab.forEach((butang) => {
            butang.addEventListener('click', () => {
                bukaTab(butang.dataset.tabButiranButang);
            });
        });

        bukaTab(butangTab[0].dataset.tabButiranButang);
    };
    const kandunganModalAset = modalButiranAset?.querySelector(
        '[data-kandungan-modal]'
    );

    const bukaButiranAset = async (url) => {
        if (!modalButiranAset || !kandunganModalAset || !url) {
            return;
        }

        kandunganModalAset.innerHTML = `
            <div class="modal-body text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-secondary mt-3 mb-0">
                    Memuatkan butiran aset...
                </p>
            </div>
        `;

        bootstrap.Modal.getOrCreateInstance(modalButiranAset).show();

        try {
            const respons = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!respons.ok) {
                throw new Error('Butiran aset tidak dapat dimuatkan.');
            }

            kandunganModalAset.innerHTML = await respons.text();
            aktifkanTabButiran(kandunganModalAset);
        } catch (ralat) {
            kandunganModalAset.innerHTML = `
                <div class="modal-body text-center py-5">
                    <i class="bi bi-exclamation-circle text-danger fs-1"></i>
                    <h5 class="mt-3">Butiran tidak dapat dimuatkan</h5>
                    <p class="text-secondary mb-0">
                        Sila cuba semula sebentar lagi.
                    </p>
                </div>
            `;
        }
    };

    document.querySelectorAll('[data-buka-butiran]').forEach((butang) => {
        butang.addEventListener('click', () => {
            bukaButiranAset(butang.dataset.url);
        });
    });

    const pemilihTindakanJadual = 'a, button, input, select, textarea, label, form';

    const aktifkanBarisJadual = (baris, tindakan) => {
        baris.addEventListener('click', (event) => {
            if (event.target.closest(pemilihTindakanJadual)) {
                return;
            }

            tindakan();
        });

        baris.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            tindakan();
        });
    };

    document.querySelectorAll('[data-baris-butiran]').forEach((baris) => {
        aktifkanBarisJadual(baris, () => {
            bukaButiranAset(baris.dataset.url);
        });
    });

    document.querySelectorAll('[data-baris-pautan]').forEach((baris) => {
        aktifkanBarisJadual(baris, () => {
            window.location.href = baris.dataset.url;
        });
    });

    const modalButiranPengguna = document.querySelector(
        '#modalButiranPengguna'
    );

    const kandunganButiranPengguna = modalButiranPengguna?.querySelector(
        '[data-kandungan-butiran-pengguna]'
    );

    const teksSelamat = (nilai) =>
        String(nilai ?? '-').replace(/[&<>"']/g, (aksara) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        })[aksara]);

    const bukaButiranPengguna = (butang) => {
        if (!modalButiranPengguna || !kandunganButiranPengguna) {
            return;
        }

        kandunganButiranPengguna.innerHTML = `
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="avatar-pengguna">
                    ${teksSelamat(butang.dataset.nama).charAt(0).toUpperCase()}
                </div>

                <div>
                    <h5 class="mb-1">${teksSelamat(butang.dataset.nama)}</h5>
                    <span class="text-secondary">Rekod pengguna sistem</span>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <div class="border rounded-3 p-3 bg-light h-100">
                        <small class="text-secondary d-block mb-1">Peranan</small>
                        <strong>${teksSelamat(butang.dataset.peranan)}</strong>
                    </div>
                </div>

                <div class="col-6">
                    <div class="border rounded-3 p-3 bg-light h-100">
                        <small class="text-secondary d-block mb-1">Status Akaun</small>
                        <strong>${teksSelamat(butang.dataset.status)}</strong>
                    </div>
                </div>

                <div class="col-12">
                    <div class="border rounded-3 p-3 bg-light">
                        <small class="text-secondary d-block mb-1">Tarikh Daftar</small>
                        <strong>${teksSelamat(butang.dataset.tarikh)}</strong>
                    </div>
                </div>
            </div>
        `;

        bootstrap.Modal
            .getOrCreateInstance(modalButiranPengguna)
            .show();
    };

    document.querySelectorAll('[data-buka-butiran-pengguna]').forEach((butang) => {
        butang.addEventListener('click', () => {
            bukaButiranPengguna(butang);
        });
    });

    document.querySelectorAll('[data-baris-butiran-pengguna]').forEach((baris) => {
        aktifkanBarisJadual(baris, () => {
            baris.querySelector('[data-buka-butiran-pengguna]')?.click();
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Kod QR
    |--------------------------------------------------------------------------
    */
    const butangSalinPautan = document.querySelector('[data-salin-pautan]');

    butangSalinPautan?.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(butangSalinPautan.dataset.pautan);

            Swal.fire({
                icon: 'success',
                title: 'Pautan berjaya disalin',
                text: 'Pautan borang aduan kini boleh dikongsi.',
                confirmButtonColor: '#168A7E',
                timer: 1800,
                timerProgressBar: true,
            });
        } catch {
            Swal.fire({
                icon: 'error',
                title: 'Pautan tidak dapat disalin',
                text: 'Sila salin pautan secara manual.',
                confirmButtonColor: '#168A7E',
            });
        }
    });

    const borangStatusQr = document.querySelector('[data-borang-status-qr]');

    borangStatusQr?.addEventListener('submit', (event) => {
        if (borangStatusQr.dataset.disahkan) {
            return;
        }

        event.preventDefault();

        const status = borangStatusQr.querySelector('[name="qr_status"]').value;
        const aktif = status === 'ACTIVE';

        Swal.fire({
            icon: 'question',
            title: aktif ? 'Aktifkan kod QR?' : 'Nyahaktifkan kod QR?',
            text: aktif
                ? 'Orang awam boleh membuat aduan melalui pautan ini.'
                : 'Orang awam tidak lagi boleh membuat aduan melalui pautan ini.',
            showCancelButton: true,
            confirmButtonText: aktif ? 'Ya, aktifkan' : 'Ya, nyahaktifkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: aktif ? '#168A7E' : '#6c757d',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then((hasil) => {
            if (hasil.isConfirmed) {
                borangStatusQr.dataset.disahkan = 'ya';
                borangStatusQr.submit();
            }
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Tindakan butiran aduan
    |--------------------------------------------------------------------------
    */
    const borangTerimaAduan = document.querySelector('[data-terima-aduan]');
    const borangTolakAduan = document.querySelector('[data-tolak-aduan]');
    const borangKemaskiniAduan = document.querySelector(
        '[data-kemaskini-aduan]'
    );

    const statusKemaskiniAduan = borangKemaskiniAduan?.querySelector(
        '[name="cmp_status"]'
    );

    const ruangGambarSiap = borangKemaskiniAduan?.querySelector(
        '#ruangGambarSiap'
    );

    

    const kemaskiniRuangGambarSiap = () => {
        if (!statusKemaskiniAduan || !ruangGambarSiap) {
            return;
        }

        const aduanSelesai = statusKemaskiniAduan.value === 'SELESAI';

        ruangGambarSiap.hidden = !aduanSelesai;

        
    };

    statusKemaskiniAduan?.addEventListener(
        'change',
        kemaskiniRuangGambarSiap
    );

    kemaskiniRuangGambarSiap();

    const panelGambarSiap = document.querySelector(
        '[data-pilihan-gambar-siap]'
    );

    if (panelGambarSiap) {
        const hadGambar = 4;

        const inputGaleri = panelGambarSiap.querySelector(
            '[data-gambar-siap-galeri]'
        );

        const bekasInputKamera = panelGambarSiap.querySelector(
            '[data-input-kamera-siap]'
        );

        const butangGaleri = panelGambarSiap.querySelector(
            '[data-buka-galeri-siap]'
        );

        const butangKamera = panelGambarSiap.querySelector(
            '[data-buka-kamera-siap]'
        );

        const statusGambar = panelGambarSiap.querySelector(
            '[data-status-gambar-siap]'
        );

        const previewGambar = panelGambarSiap.querySelector(
            '[data-preview-gambar-siap]'
        );

        const dapatGambarGaleri = () =>
            [...(inputGaleri.files ?? [])].map((fail, indeks) => ({
                sumber: 'galeri',
                id: `galeri-siap-${indeks}`,
                indeks,
                fail,
            }));

        const dapatGambarKamera = () =>
            [...bekasInputKamera.querySelectorAll('[data-gambar-kamera-siap]')]
                .filter((input) => input.files?.length > 0)
                .map((input) => ({
                    sumber: 'kamera',
                    id: input.dataset.id,
                    fail: input.files[0],
                }));

        const semuaGambar = () => [
            ...dapatGambarGaleri(),
            ...dapatGambarKamera(),
        ];

        const jumlahGambarDipilih = () => semuaGambar().length;

        const paparRalatHadGambar = () => {
            Swal.fire({
                icon: 'warning',
                title: 'Had gambar telah dicapai',
                text: 'Maksimum empat gambar sahaja boleh dimuat naik.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#168A7E',
            });
        };

        const kemaskiniFailGaleri = (fail) => {
            const pemindahanFail = new DataTransfer();

            fail.forEach((item) => {
                pemindahanFail.items.add(item);
            });

            inputGaleri.files = pemindahanFail.files;
        };

        const padamGambar = (gambar) => {
            if (gambar.sumber === 'galeri') {
                const failGaleri = [...inputGaleri.files];

                failGaleri.splice(gambar.indeks, 1);

                kemaskiniFailGaleri(failGaleri);
            } else {
                bekasInputKamera
                    .querySelector(
                        `[data-gambar-kamera-siap][data-id="${gambar.id}"]`
                    )
                    ?.remove();
            }

            kemaskiniPaparanGambar();
        };

        const kemaskiniPreviewGambar = () => {
            previewGambar.replaceChildren();

            semuaGambar().forEach((gambar, indeks) => {
                const kadPreview = document.createElement('div');

                kadPreview.className = 'item-preview-gambar';

                const imej = document.createElement('img');

                imej.src = URL.createObjectURL(gambar.fail);
                imej.alt = `Gambar selepas penyelenggaraan ${indeks + 1}`;

                const butangPadam = document.createElement('button');

                butangPadam.type = 'button';
                butangPadam.className = 'butang-buang-preview';
                butangPadam.title = `Buang gambar ${indeks + 1}`;
                butangPadam.setAttribute(
                    'aria-label',
                    `Buang gambar ${indeks + 1}`
                );
                butangPadam.innerHTML = '<i class="bi bi-x-lg"></i>';

                butangPadam.addEventListener('click', () => {
                    padamGambar(gambar);
                });

                kadPreview.append(imej, butangPadam);
                previewGambar.appendChild(kadPreview);
            });
        };

        const kemaskiniPaparanGambar = () => {
            const jumlah = jumlahGambarDipilih();

            statusGambar.textContent =
                `${jumlah} daripada ${hadGambar} gambar dipilih. ` +
                'Saiz setiap gambar tidak melebihi 5 MB.';

            butangKamera.disabled = jumlah >= hadGambar;
            butangKamera.classList.toggle('disabled', jumlah >= hadGambar);

            kemaskiniPreviewGambar();
        };

        butangGaleri.addEventListener('click', () => {
            inputGaleri.click();
        });

        inputGaleri.addEventListener('change', () => {
            const jumlahKamera = dapatGambarKamera().length;
            const bakiGambar = hadGambar - jumlahKamera;
            const failDipilih = [...inputGaleri.files];

            if (failDipilih.length > bakiGambar) {
                kemaskiniFailGaleri(
                    failDipilih.slice(0, Math.max(0, bakiGambar))
                );

                paparRalatHadGambar();
            }

            kemaskiniPaparanGambar();
        });

        butangKamera.addEventListener('click', () => {
            if (jumlahGambarDipilih() >= hadGambar) {
                paparRalatHadGambar();
                return;
            }

            const inputKamera = document.createElement('input');

            inputKamera.type = 'file';
            inputKamera.name = 'gambar_siap[]';
            inputKamera.accept = 'image/jpeg,image/png,image/webp';
            inputKamera.capture = 'environment';
            inputKamera.hidden = true;
            inputKamera.dataset.gambarKameraSiap = 'ya';
            inputKamera.dataset.id =
                window.crypto?.randomUUID?.() ?? `${Date.now()}-${Math.random()}`;

            inputKamera.addEventListener('change', () => {
                if (!inputKamera.files?.length) {
                    inputKamera.remove();
                } else if (jumlahGambarDipilih() > hadGambar) {
                    inputKamera.remove();
                    paparRalatHadGambar();
                }

                kemaskiniPaparanGambar();
            });

            bekasInputKamera.appendChild(inputKamera);
            inputKamera.click();
        });

        kemaskiniPaparanGambar();
    }

    const sahkanTindakanAduan = (borang, tetapan) => {
        borang?.addEventListener('submit', (event) => {
            if (borang.dataset.disahkan) {
                return;
            }

            event.preventDefault();

            if (!borang.checkValidity()) {
                borang.reportValidity();
                return;
            }

            Swal.fire({
                icon: tetapan.icon,
                title: tetapan.tajuk,
                text: tetapan.teks,
                showCancelButton: true,
                confirmButtonText: tetapan.sahkan,
                cancelButtonText: 'Batal',
                confirmButtonColor: tetapan.warna,
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
            }).then((hasil) => {
                if (hasil.isConfirmed) {
                    borang.dataset.disahkan = 'ya';
                    borang.submit();
                }
            });
        });
    };

    sahkanTindakanAduan(borangTerimaAduan, {
        icon: 'question',
        tajuk: 'Terima aduan ini?',
        teks: 'Aduan akan dihantar kepada Jabatan Kejuruteraan untuk tindakan lanjut.',
        sahkan: 'Ya, terima',
        warna: '#168A7E',
    });

    sahkanTindakanAduan(borangTolakAduan, {
        icon: 'warning',
        tajuk: 'Tolak aduan ini?',
        teks: 'Aduan akan ditandakan sebagai selesai dan tidak dihantar kepada KJ.',
        sahkan: 'Ya, tolak',
        warna: '#dc3545',
    });

    sahkanTindakanAduan(borangKemaskiniAduan, {
        icon: 'question',
        tajuk: 'Simpan kemaskini penyelenggaraan?',
        teks: 'Maklumat tindakan KJ akan direkodkan pada aduan ini.',
        sahkan: 'Ya, simpan',
        warna: '#168A7E',
    });

    /*
    |--------------------------------------------------------------------------
    | Penapis automatik halaman senarai
    |--------------------------------------------------------------------------
    */
    document
        .querySelectorAll('[data-penapis-automatik]')
        .forEach((borang) => {
            let pemasaCarian;

            const hantarCarian = () => {
                window.clearTimeout(pemasaCarian);
                borang.submit();
            };

            const medanCarian = borang.querySelector(
                'input[type="search"][name="cari"]'
            );

            const dropdownPenapis = borang.querySelectorAll(
                'select.kawalan-penapis'
            );

            const medanTarikh = borang.querySelectorAll(
                'input[type="date"].kawalan-penapis'
            );

            medanTarikh.forEach((medan) => {
                medan.addEventListener('change', hantarCarian);
            });

            dropdownPenapis.forEach((dropdown) => {
                dropdown.addEventListener('change', hantarCarian);
            });

            medanCarian?.addEventListener('input', () => {
                window.clearTimeout(pemasaCarian);

                pemasaCarian = window.setTimeout(() => {
                    borang.submit();
                }, 400);
            });

            medanCarian?.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    hantarCarian();
                }
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Papar atau sorok kata laluan
    |--------------------------------------------------------------------------
    */
    document
        .querySelectorAll('[data-togol-kata-laluan]')
        .forEach((butang) => {
            const input = document.querySelector(
                butang.dataset.togolKataLaluan
            );

            if (!input) {
                return;
            }

            butang.addEventListener('click', () => {
                const sedangDisorok = input.type === 'password';

                input.type = sedangDisorok ? 'text' : 'password';
                butang.setAttribute(
                    'aria-label',
                    sedangDisorok
                        ? 'Sorok kata laluan'
                        : 'Papar kata laluan'
                );
                butang.setAttribute(
                    'title',
                    sedangDisorok
                        ? 'Sorok kata laluan'
                        : 'Papar kata laluan'
                );

                butang.querySelector('i').className = sedangDisorok
                    ? 'bi bi-eye-slash'
                    : 'bi bi-eye';
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Suis status akaun pengguna
    |--------------------------------------------------------------------------
    */
    document
        .querySelectorAll('[data-suis-status-wrapper]')
        .forEach((wrapper) => {
            const suis = wrapper.querySelector('[data-suis-status]');
            const nilai = wrapper.querySelector('[data-nilai-status]');
            const label = wrapper.querySelector('[data-label-status]');
            const penerangan = wrapper.querySelector('[data-penerangan-status]');

            const kemaskiniStatus = () => {
                const aktif = suis.checked;

                nilai.value = aktif ? 'ACTIVE' : 'INACTIVE';
                wrapper.classList.toggle('aktif', aktif);
                wrapper.classList.toggle('tidak-aktif', !aktif);

                label.textContent = aktif ? 'Akaun Aktif' : 'Akaun Tidak Aktif';
                penerangan.textContent = aktif
                    ? 'Pengguna boleh mengakses sistem.'
                    : 'Akses pengguna disekat buat sementara waktu.';
            };

            suis.addEventListener('change', kemaskiniStatus);
        });

    /*
    |--------------------------------------------------------------------------
    | Lightbox gambar aduan
    |--------------------------------------------------------------------------
    */
    const butangGambarAduan = [
        ...document.querySelectorAll('[data-buka-lightbox-aduan]'),
    ];

    const modalLightboxAduan = document.querySelector('#modalLightboxAduan');

    if (butangGambarAduan.length && modalLightboxAduan) {
        const gambarLightbox = modalLightboxAduan.querySelector(
            '[data-lightbox-gambar]'
        );
        const bilanganLightbox = modalLightboxAduan.querySelector(
            '[data-lightbox-bilangan]'
        );
        const butangSebelum = modalLightboxAduan.querySelector(
            '[data-lightbox-sebelum]'
        );
        const butangSeterusnya = modalLightboxAduan.querySelector(
            '[data-lightbox-seterusnya]'
        );

        let indeksSemasa = 0;

        const paparGambar = (indeks) => {
            indeksSemasa = (indeks + butangGambarAduan.length)
                % butangGambarAduan.length;

            const item = butangGambarAduan[indeksSemasa];

            gambarLightbox.src = item.dataset.sumber;
            gambarLightbox.alt = item.querySelector('img')?.alt
                ?? 'Gambar kerosakan aduan';

            const kemaskiniNisbahGambar = () => {
                const gambarPotret =
                    gambarLightbox.naturalHeight > gambarLightbox.naturalWidth;

                modalLightboxAduan.classList.toggle(
                    'gambar-potret',
                    gambarPotret
                );
            };

            gambarLightbox.onload = kemaskiniNisbahGambar;

            if (gambarLightbox.complete) {
                kemaskiniNisbahGambar();
            }

            bilanganLightbox.textContent =
                `${indeksSemasa + 1} daripada ${butangGambarAduan.length}`;

            const satuGambarSahaja = butangGambarAduan.length === 1;

            butangSebelum.disabled = satuGambarSahaja;
            butangSeterusnya.disabled = satuGambarSahaja;
        };

        butangGambarAduan.forEach((butang, indeks) => {
            butang.addEventListener('click', () => {
                paparGambar(indeks);

                bootstrap.Modal
                    .getOrCreateInstance(modalLightboxAduan)
                    .show();
            });
        });

        butangSebelum.addEventListener('click', () => {
            paparGambar(indeksSemasa - 1);
        });

        butangSeterusnya.addEventListener('click', () => {
            paparGambar(indeksSemasa + 1);
        });

        modalLightboxAduan.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') {
                paparGambar(indeksSemasa - 1);
            }

            if (event.key === 'ArrowRight') {
                paparGambar(indeksSemasa + 1);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Borang aset bangunan
    |--------------------------------------------------------------------------
    */
    const borangBangunan = document.querySelector('[data-borang-bangunan]');

    if (!borangBangunan) {
        return;
    }

    const medanFormatTajukBangunan = [
        'asb_name',
        'asb_add1',
        'asb_add2',
        'asb_city',
        'asb_collector_name',
        'asb_collector_position',
        'asb_verifier_name',
        'asb_verifier_position',
    ];

    const formatTajukBangunan = (nilai) => {
        return nilai
            .toLocaleLowerCase('ms-MY')
            .replace(
                /(^|[\s/-])(\p{L})/gu,
                (padanan, pemisah, huruf) =>
                    `${pemisah}${huruf.toLocaleUpperCase('ms-MY')}`
            );
    };

    medanFormatTajukBangunan.forEach((namaMedan) => {
        const medan = borangBangunan.querySelector(
            `input[name="${namaMedan}"]`
        );

        if (!medan) {
            return;
        }

        const normalisasikanNilai = () => {
            const kedudukanMula = medan.selectionStart;
            const kedudukanAkhir = medan.selectionEnd;
            const nilaiBaharu = formatTajukBangunan(medan.value);

            if (medan.value === nilaiBaharu) {
                return;
            }

            medan.value = nilaiBaharu;

            if (
                document.activeElement === medan &&
                kedudukanMula !== null &&
                kedudukanAkhir !== null
            ) {
                medan.setSelectionRange(kedudukanMula, kedudukanAkhir);
            }
        };

        medan.addEventListener('input', normalisasikanNilai);
        medan.addEventListener('blur', normalisasikanNilai);

        normalisasikanNilai();
    });

    const panelBangunan = [
        ...borangBangunan.querySelectorAll('[data-langkah]'),
    ];

    const penunjukBangunan = [
        ...borangBangunan.querySelectorAll('[data-penunjuk-bangunan]'),
    ];

    const kemajuanBangunan = borangBangunan.querySelector(
        '[data-kemajuan-bangunan]'
    );

    const butangSeterusnya = [
        ...borangBangunan.querySelectorAll('[data-seterusnya]'),
    ];

    const butangSebelum = [
        ...borangBangunan.querySelectorAll('[data-sebelum]'),
    ];

    let langkahBangunanSemasa = 1;

    const kemaskiniPenunjukBangunan = () => {
        penunjukBangunan.forEach((item) => {
            const nomborItem = Number(item.dataset.penunjukBangunan);

            item.classList.toggle(
                'aktif',
                nomborItem === langkahBangunanSemasa
            );

            item.classList.toggle(
                'selesai',
                nomborItem < langkahBangunanSemasa
            );
        });

        if (kemajuanBangunan) {
            const jumlahPeralihan = Math.max(panelBangunan.length -1, 1);
            const peratus = ((langkahBangunanSemasa -1) / jumlahPeralihan) * 100;
            kemajuanBangunan.style.width = `${peratus}%`;
        }
    };

    const paparkanLangkahBangunan = (nombor, arah = 'kanan') => {
        const semasa = borangBangunan.querySelector(
            `[data-langkah="${langkahBangunanSemasa}"]`
        );

        const sasaran = borangBangunan.querySelector(
            `[data-langkah="${nombor}"]`
        );

        if (!sasaran || nombor === langkahBangunanSemasa) {
            return;
        }

        if (semasa) {
            semasa.classList.remove('aktif');
            semasa.classList.add(
                arah === 'kanan' ? 'keluar-ke-kiri' : 'keluar-ke-kanan'
            );
        }

        window.setTimeout(() => {
            if (semasa) {
                semasa.classList.remove('keluar-ke-kiri', 'keluar-ke-kanan');
            }

            langkahBangunanSemasa = nombor;

            sasaran.classList.add('aktif');
            sasaran.classList.add(
                arah === 'kanan' ? 'masuk-dari-kanan' : 'masuk-dari-kiri'
            );

            kemaskiniPenunjukBangunan();

            window.setTimeout(() => {
                sasaran.classList.remove('masuk-dari-kanan', 'masuk-dari-kiri');
            }, 240);
        }, 240);
    };

    const sahkanLangkahBangunan = () => {
        const langkahAktif = borangBangunan.querySelector(
            `[data-langkah="${langkahBangunanSemasa}"]`
        );

        if (!langkahAktif) {
            return true;
        }

        const medan = [
            ...langkahAktif.querySelectorAll('input, select, textarea'),
        ];

        const tidakSah = medan.find((item) => !item.checkValidity());

        if (!tidakSah) {
            return true;
        }

        tidakSah.reportValidity();

        Swal.fire({
            icon: 'warning',
            title: 'Maklumat belum lengkap',
            text: 'Sila lengkapkan maklumat wajib sebelum meneruskan.',
            confirmButtonColor: '#168A7E',
        });

        return false;
    };

    butangSeterusnya.forEach((butang) => {
        butang.addEventListener('click', () => {
            if (
                sahkanLangkahBangunan() &&
                langkahBangunanSemasa < panelBangunan.length
            ) {
                paparkanLangkahBangunan(langkahBangunanSemasa + 1);
            }
        });
    });

    butangSebelum.forEach((butang) => {
        butang.addEventListener('click', () => {
            if (langkahBangunanSemasa > 1) {
                paparkanLangkahBangunan(
                    langkahBangunanSemasa - 1,
                    'kiri'
                );
            }
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Penyewa dinamik
    |--------------------------------------------------------------------------
    */
    const senaraiPenyewa = borangBangunan.querySelector(
        '[data-senarai-penyewa]'
    );

    const butangTambahPenyewa = borangBangunan.querySelector(
        '[data-tambah-penyewa]'
    );

    const templatPenyewa = document.querySelector('#templatPenyewa');

    const kemaskiniMesejPenyewa = () => {
        if (!senaraiPenyewa) {
            return;
        }

        const mesej = senaraiPenyewa.querySelector('[data-mesej-penyewa]');
        const jumlahPenyewa = senaraiPenyewa.querySelectorAll(
            '[data-penyewa]'
        ).length;

        if (jumlahPenyewa === 0 && !mesej) {
            senaraiPenyewa.insertAdjacentHTML(
                'beforeend',
                `
                    <div class="col-12" data-mesej-penyewa>
                        <div class="kad-kosong py-4">
                            <div class="ilustrasi-kosong">
                                <i class="bi bi-people"></i>
                            </div>
                            <h3>Tiada maklumat penyewa</h3>
                            <p>Tekan butang “Tambah Penyewa” jika aset ini mempunyai penyewa.</p>
                        </div>
                    </div>
                `
            );
        }

        if (jumlahPenyewa > 0 && mesej) {
            mesej.remove();
        }
    };

    butangTambahPenyewa?.addEventListener('click', () => {
        if (!senaraiPenyewa || !templatPenyewa) {
            return;
        }

        const indeks = Number(
            senaraiPenyewa.dataset.indeksSeterusnya || 0
        );

        const kandungan = templatPenyewa.innerHTML.replaceAll(
            '__INDEX__',
            indeks
        );

        senaraiPenyewa.insertAdjacentHTML('beforeend', kandungan);
        senaraiPenyewa.dataset.indeksSeterusnya = indeks + 1;

        kemaskiniMesejPenyewa();

        const penyewaBaharu = senaraiPenyewa.lastElementChild;

        penyewaBaharu?.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
        });
    });

    senaraiPenyewa?.addEventListener('click', (event) => {
        const butangBuang = event.target.closest('[data-buang-penyewa]');

        if (!butangBuang) {
            return;
        }

        const kadPenyewa = butangBuang.closest('[data-penyewa]');

        Swal.fire({
            icon: 'warning',
            title: 'Buang maklumat penyewa?',
            text: 'Maklumat penyewa ini tidak akan disimpan dalam borang.',
            showCancelButton: true,
            confirmButtonText: 'Ya, buang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then((hasil) => {
            if (!hasil.isConfirmed || !kadPenyewa) {
                return;
            }

            kadPenyewa.style.opacity = '0';
            kadPenyewa.style.transform = 'translateY(-8px)';

            window.setTimeout(() => {
                kadPenyewa.remove();
                kemaskiniMesejPenyewa();
            }, 180);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Dokumen bangunan dinamik
    |--------------------------------------------------------------------------
    */
    const senaraiDokumen = borangBangunan.querySelector(
        '[data-senarai-dokumen]'
    );

    const butangTambahDokumen = borangBangunan.querySelector(
        '[data-tambah-dokumen]'
    );

    const templatDokumen = document.querySelector('#templatDokumen');

    const kemaskiniMesejDokumen = () => {
        if (!senaraiDokumen) {
            return;
        }

        const mesej = senaraiDokumen.querySelector('[data-mesej-dokumen]');
        const jumlahDokumen = senaraiDokumen.querySelectorAll(
            '[data-dokumen]'
        ).length;

        if (jumlahDokumen === 0 && !mesej) {
            senaraiDokumen.insertAdjacentHTML(
                'beforeend',
                `
                    <div class="col-12" data-mesej-dokumen>
                        <div class="kad-kosong py-4">
                            <div class="ilustrasi-kosong">
                                <i class="bi bi-folder2-open"></i>
                            </div>
                            <h3>Tiada dokumen baharu</h3>
                            <p>Tekan “Tambah Dokumen” jika ada dokumen untuk dimuat naik.</p>
                        </div>
                    </div>
                `
            );
        }

        if (jumlahDokumen > 0 && mesej) {
            mesej.remove();
        }
    };

    butangTambahDokumen?.addEventListener('click', () => {
        if (!senaraiDokumen || !templatDokumen) {
            return;
        }

        const indeks = Number(
            senaraiDokumen.dataset.indeksSeterusnya || 0
        );

        const kandungan = templatDokumen.innerHTML.replaceAll(
            '__INDEX__',
            indeks
        );

        senaraiDokumen.insertAdjacentHTML('beforeend', kandungan);
        senaraiDokumen.dataset.indeksSeterusnya = indeks + 1;

        kemaskiniMesejDokumen();
    });

    senaraiDokumen?.addEventListener('click', (event) => {
        const butangBuang = event.target.closest('[data-buang-dokumen]');

        if (!butangBuang) {
            return;
        }

        const kadDokumen = butangBuang.closest('[data-dokumen]');

        Swal.fire({
            icon: 'warning',
            title: 'Buang dokumen baharu?',
            text: 'Dokumen ini tidak akan dimuat naik.',
            showCancelButton: true,
            confirmButtonText: 'Ya, buang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then((hasil) => {
            if (!hasil.isConfirmed || !kadDokumen) {
                return;
            }

            kadDokumen.style.opacity = '0';
            kadDokumen.style.transform = 'translateY(-8px)';

            window.setTimeout(() => {
                kadDokumen.remove();
                kemaskiniMesejDokumen();
            }, 180);
        });
    });

    document.querySelectorAll('[data-padam-dokumen]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Padam dokumen?',
                html: `Dokumen <strong>${form.dataset.namaDokumen}</strong> akan dipadam secara kekal.`,
                showCancelButton: true,
                confirmButtonText: 'Ya, padam',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
            }).then((hasil) => {
                if (hasil.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    kemaskiniMesejDokumen();

    /*
    |--------------------------------------------------------------------------
    | Pengesahan simpan aset bangunan
    |--------------------------------------------------------------------------
    */
    borangBangunan.addEventListener('submit', (event) => {
        if (borangBangunan.dataset.disahkan) {
            return;
        }

        event.preventDefault();

        const medanTidakSah = [
            ...borangBangunan.querySelectorAll('input, select, textarea'),
        ].find((item) => !item.checkValidity());

        if (medanTidakSah) {
            const seksyen = medanTidakSah.closest('[data-langkah]');
            const nomborLangkah = Number(seksyen?.dataset.langkah);

            if (
                nomborLangkah &&
                nomborLangkah !== langkahBangunanSemasa
            ) {
                paparkanLangkahBangunan(nomborLangkah);
            }

            window.setTimeout(() => medanTidakSah.reportValidity(), 250);

            Swal.fire({
                icon: 'warning',
                title: 'Maklumat belum lengkap',
                text: 'Sila semak semula ruangan yang wajib diisi.',
                confirmButtonColor: '#168A7E',
            });

            return;
        }

        Swal.fire({
            icon: 'question',
            title: 'Simpan aset bangunan?',
            text: 'Pastikan semua maklumat yang dimasukkan adalah betul.',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Semak semula',
            confirmButtonColor: '#168A7E',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
        }).then((hasil) => {
            if (hasil.isConfirmed) {
                borangBangunan.dataset.disahkan = 'ya';
                borangBangunan.submit();
            }
        });
    });

    kemaskiniPenunjukBangunan();
    kemaskiniMesejPenyewa();
    
    /*
    |--------------------------------------------------------------------------
    | Elak hantar borang dua kali
    |--------------------------------------------------------------------------
    |
    */
    const pemilihButangHantar = 'button[type="submit"], button:not([type]), input[type="submit"]';

    const borangPerluDikunci = (borang) => borang instanceof HTMLFormElement
        && borang.method.toLowerCase() !== 'get'
        && borang.target !== '_blank'
        && ! borang.hasAttribute('data-tanpa-kunci');

    // Ingat butang mana yang diklik (untuk paparan "Menyimpan...")
    document.addEventListener('click', (event) => {
        const butang = event.target.closest(pemilihButangHantar);

        if (butang?.form) {
            butang.form.butangDiklik = butang;
        }
    }, true);

    // Sekat hantaran kedua semasa borang sedang dihantar
    document.addEventListener('submit', (event) => {
        if (event.target.dataset?.sedangHantar === '1') {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);

    // Kunci butang bila borang benar-benar dihantar
    document.addEventListener('formdata', (event) => {
        const borang = event.target;

        if (! borangPerluDikunci(borang)) {
            return;
        }

        borang.dataset.sedangHantar = '1';

        const butangUtama = borang.butangDiklik ?? borang.querySelector(pemilihButangHantar);

        // Tunggu senarai data borang siap dibina, kemudian kunci
        setTimeout(() => {
            borang.querySelectorAll(pemilihButangHantar).forEach((butang) => {
                butang.disabled = true;
            });

            if (butangUtama && butangUtama.tagName === 'BUTTON') {
                butangUtama.dataset.teksAsal = butangUtama.innerHTML;
                butangUtama.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${butangUtama.dataset.teksMenghantar ?? 'Menyimpan...'}`;
            }
        }, 0);
    }, true);

    // Pulihkan butang bila pengguna kembali dengan butang Back pelayar
    window.addEventListener('pageshow', (event) => {
        if (! event.persisted) {
            return;
        }

        document.querySelectorAll('form[data-sedang-hantar="1"]').forEach((borang) => {
            delete borang.dataset.sedangHantar;

            borang.querySelectorAll(pemilihButangHantar).forEach((butang) => {
                butang.disabled = false;

                if (butang.dataset.teksAsal) {
                    butang.innerHTML = butang.dataset.teksAsal;
                    delete butang.dataset.teksAsal;
                }
            });
        });
    });
});