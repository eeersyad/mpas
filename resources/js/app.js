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

        // Kemaskini sahaja: tekan penunjuk langkah untuk terus ke bahagian itu
        if (borangTanah.querySelector('input[name="_method"]')) {
            penunjuk.forEach((item, indeks) => {
                item.classList.add('boleh-klik');
                item.setAttribute('role', 'button');
                item.setAttribute('tabindex', '0');

                const pergi = () => tukarLangkah(indeks, indeks > langkahSemasa ? 'seterusnya' : 'sebelumnya');

                item.addEventListener('click', pergi);
                item.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        pergi();
                    }
                });
            });
        }

        // Buka terus langkah tertentu, contoh: /aset-tanah/5/edit?langkah=5
        const langkahUrlTanah = Number(new URLSearchParams(window.location.search).get('langkah'));

        if (langkahUrlTanah > 1 && langkahUrlTanah <= panel.length) {
            window.setTimeout(() => tukarLangkah(langkahUrlTanah - 1, 'seterusnya'), 50);
            // Buang ?langkah= daripada URL supaya refresh bermula seperti biasa
            const urlBersih = new URL(window.location.href);
            urlBersih.searchParams.delete('langkah');
            window.history.replaceState(window.history.state, '', urlBersih);
        }

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

        modalButiranPengguna.querySelectorAll('[data-pautan-kemaskini-pengguna]').forEach((pautan) => {
            pautan.href = butang.dataset.urlKemaskini || '#';
        });

        const aktif = butang.dataset.status === 'Aktif';

        // Paparan telefon (gaya aplikasi)
        const paparanTelefon = `
            <div class="d-md-none">
                <div class="hero-pengguna-apl">
                    <div class="avatar-pengguna-besar">
                        ${teksSelamat(butang.dataset.nama).charAt(0).toUpperCase()}
                    </div>
                    <h2>${teksSelamat(butang.dataset.nama)}</h2>
                    <div class="d-flex justify-content-center flex-wrap gap-1">
                        <span class="lencana-jadual kelabu">${teksSelamat(butang.dataset.peranan)}</span>
                        <span class="lencana-jadual ${aktif ? 'utama' : 'kelabu'}">${teksSelamat(butang.dataset.status)}</span>
                    </div>
                </div>

                <div class="px-3 pb-3">
                    <p class="label-kumpulan-apl">Maklumat akaun</p>
                    <div class="kad-senarai-apl">
                        <div class="baris-senarai-apl">
                            <span>Peranan</span>
                            <strong>${teksSelamat(butang.dataset.namaPeranan || butang.dataset.peranan)}</strong>
                        </div>
                        <div class="baris-senarai-apl">
                            <span>Status</span>
                            <strong>${teksSelamat(butang.dataset.status)}</strong>
                        </div>
                        <div class="baris-senarai-apl">
                            <span>Tarikh daftar</span>
                            <strong>${teksSelamat(butang.dataset.tarikh)}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;

        kandunganButiranPengguna.innerHTML = paparanTelefon + `<div class="d-none d-md-block">` + `
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
        ` + `</div>`;

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

    // Pasang klik pada baris yang ditambah kemudian (contohnya "Muat lagi" di telefon)
    window.mpasPasangBaris = (baris) => {
        if (baris.matches('[data-baris-butiran]')) {
            aktifkanBarisJadual(baris, () => bukaButiranAset(baris.dataset.url));
        } else if (baris.matches('[data-baris-pautan]')) {
            aktifkanBarisJadual(baris, () => {
                window.location.href = baris.dataset.url;
            });
        } else if (baris.matches('[data-baris-butiran-pengguna]')) {
            aktifkanBarisJadual(baris, () => {
                baris.querySelector('[data-buka-butiran-pengguna]')?.click();
            });
        }

        baris.querySelectorAll('[data-buka-butiran-pengguna]').forEach((butang) => {
            butang.addEventListener('click', () => bukaButiranPengguna(butang));
        });
    };

    /*
    |--------------------------------------------------------------------------
    | Kod QR
    |--------------------------------------------------------------------------
    */
    const senaraiButangSalin = document.querySelectorAll('[data-salin-pautan]');

    // Salin teks: guna Clipboard API jika ada (HTTPS / localhost),
    // jika tidak guna cara lama (sesuai untuk http:// dengan alamat IP)
    const salinTeks = async (teks) => {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(teks);
            return;
        }

        const medan = document.createElement('textarea');
        medan.value = teks;
        medan.setAttribute('readonly', '');
        medan.style.position = 'fixed';
        medan.style.top = '-9999px';
        document.body.appendChild(medan);
        medan.select();

        const berjaya = document.execCommand('copy');
        medan.remove();

        if (! berjaya) {
            throw new Error('Salin gagal');
        }
    };

    const salinPautan = async (pautan) => {
        try {
            await salinTeks(pautan);

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
    };

    senaraiButangSalin.forEach((butang) => {
        butang.addEventListener('click', () => salinPautan(butang.dataset.pautan));
    });

    // Kongsi (telefon): menu kongsi asli telefon; jika tiada (contohnya http://), salin pautan
    document.querySelectorAll('[data-kongsi-pautan]').forEach((butang) => {
        butang.addEventListener('click', async () => {
            if (navigator.share && window.isSecureContext) {
                try {
                    await navigator.share({
                        title: butang.dataset.tajuk || 'Borang aduan',
                        text: 'Borang aduan aset Majlis Daerah Marang',
                        url: butang.dataset.pautan,
                    });
                } catch {
                    // Pengguna membatalkan menu kongsi
                }

                return;
            }

            salinPautan(butang.dataset.pautan);
        });
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

    // Suis status QR (telefon): pengesahan sama seperti borang desktop
    const borangSuisQr = document.querySelector('[data-suis-status-qr]');
    const suisQr = borangSuisQr?.querySelector('#suisStatusQr');

    suisQr?.addEventListener('change', () => {
        const aktif = suisQr.checked;

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
            if (!hasil.isConfirmed) {
                suisQr.checked = !aktif;
                return;
            }

            borangSuisQr.querySelector('[name="qr_status"]').value = aktif ? 'ACTIVE' : 'INACTIVE';
            suisQr.disabled = true;
            borangSuisQr.submit();
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

    // Buka terus langkah tertentu, contoh: /aset-bangunan/5/edit?langkah=8
    const langkahUrlBangunan = Number(new URLSearchParams(window.location.search).get('langkah'));

    if (langkahUrlBangunan > 1 && langkahUrlBangunan <= panelBangunan.length) {
        window.setTimeout(() => paparkanLangkahBangunan(langkahUrlBangunan, 'kanan'), 50);
        // Buang ?langkah= daripada URL supaya refresh bermula seperti biasa
        const urlBersih = new URL(window.location.href);
        urlBersih.searchParams.delete('langkah');
        window.history.replaceState(window.history.state, '', urlBersih);
    }

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

    // Kemaskini sahaja: tekan penunjuk langkah untuk terus ke bahagian itu
    // (borang daftar kekal ikut turutan supaya tiada maklumat tertinggal)
    if (borangBangunan.querySelector('input[name="_method"]')) {
        penunjukBangunan.forEach((item) => {
            const nombor = Number(item.dataset.penunjukBangunan);

            item.classList.add('boleh-klik');
            item.setAttribute('role', 'button');
            item.setAttribute('tabindex', '0');

            const pergi = () => paparkanLangkahBangunan(nombor, nombor > langkahBangunanSemasa ? 'kanan' : 'kiri');

            item.addEventListener('click', pergi);
            item.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    pergi();
                }
            });
        });
    }

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

    // Kunci butang bila borang benar-benar dihantar.
    // Nota: JANGAN guna event 'formdata' — ia juga berlaku setiap kali skrip membaca
    // borang dengan new FormData() (contoh: simpan draf automatik), lalu butang terkunci.
    document.addEventListener('submit', (event) => {
        const borang = event.target;

        if (! borangPerluDikunci(borang)) {
            return;
        }

        const butangUtama = borang.butangDiklik ?? borang.querySelector(pemilihButangHantar);

        // Tunggu semua pendengar submit lain selesai; jika ada yang membatalkan
        // (pengesahan / dialog), jangan kunci butang.
        setTimeout(() => {
            if (event.defaultPrevented) {
                return;
            }

            borang.dataset.sedangHantar = '1';

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

/*
|--------------------------------------------------------------------------
| Jadual sebagai kad di telefon
|--------------------------------------------------------------------------
| Salin tajuk kolum ke setiap sel (data-label) supaya CSS boleh
| memaparkan setiap baris sebagai kad pada skrin kecil.
| Jadual yang mempunyai <tfoot> (contohnya jumlah laporan) dikecualikan.
*/
const sediakanKadJadual = (jadual, senaraiBaris = null) => {
    if (jadual.tFoot || jadual.dataset.tanpaKad !== undefined) {
        return;
    }

    const barisDiproses = senaraiBaris ?? [...jadual.querySelectorAll('tbody tr')];

    // Kad gaya aplikasi: peranan setiap kolum ditetapkan dalam data-kad-apl
    // Contoh: "sembunyi|sub|tajuk|meta=No. fail:|lencana|tindakan"
    if (jadual.dataset.kadApl) {
        const peranan = jadual.dataset.kadApl.split('|').map((item) => {
            const [nama, ...awalan] = item.split('=');
            return { nama: nama.trim(), awalan: awalan.join('=').trim() };
        });

        jadual.classList.add('jadual-kad-apl');

        barisDiproses.forEach((baris) => {
            [...baris.children].forEach((sel, indeks) => {
                if (sel.hasAttribute('colspan') || !peranan[indeks]) {
                    return;
                }

                sel.dataset.kad = peranan[indeks].nama;

                if (peranan[indeks].awalan) {
                    sel.dataset.awalan = peranan[indeks].awalan;
                }
            });
        });

        return;
    }

    const tajukKolum = [...jadual.querySelectorAll('thead th')].map((th) =>
        th.innerText.replace(/\s+/g, ' ').trim()
    );

    jadual.classList.add('jadual-kad');

    barisDiproses.forEach((baris) => {
        [...baris.children].forEach((sel, indeks) => {
            if (!sel.hasAttribute('data-label') && !sel.hasAttribute('colspan')) {
                sel.setAttribute('data-label', tajukKolum[indeks] ?? '');
            }
        });
    });
};

document.querySelectorAll('table.jadual-sistem').forEach((jadual) => sediakanKadJadual(jadual));

// Bottom sheet penapis (telefon): tukar pilihan susun kepada sort & direction,
// dan jangan hantar parameter kosong supaya URL kekal kemas
document.querySelectorAll('[data-penapis-mobile-borang]').forEach((borang) => {
    borang.addEventListener('submit', () => {
        const susun = borang.querySelector('input[name="_susun"]:checked')?.value ?? '';
        const [medan = '', arah = ''] = susun.split('|');

        borang.querySelector('[data-susun-sort]').value = medan;
        borang.querySelector('[data-susun-arah]').value = arah;

        borang.querySelectorAll('input[name="_susun"]').forEach((input) => {
            input.disabled = true;
        });

        borang.querySelectorAll('input').forEach((input) => {
            if (input.name && input.value === '' && (input.type !== 'radio' || input.checked)) {
                input.disabled = true;
            }
        });
    });
});

// Menu titik tiga pada kad (telefon): "Lihat butiran" = sama seperti tekan kad
document.addEventListener('click', (event) => {
    const butang = event.target.closest('[data-buka-butiran-kad]');

    if (butang) {
        butang.closest('tr')?.click();
    }
});

/*
|--------------------------------------------------------------------------
| Muat lagi / tatal tanpa henti (telefon)
|--------------------------------------------------------------------------
| Bila pengguna sampai ke hujung senarai, halaman seterusnya diambil dan
| barisnya ditambah ke senarai sedia ada. Butang "Muat lagi" kekal sebagai
| sandaran. Desktop menggunakan paginasi biasa.
*/
document.addEventListener('DOMContentLoaded', () => {
    const skrinTelefon = window.matchMedia('(max-width: 767.98px)');

    document.querySelectorAll('[data-muat-lagi]').forEach((bekas) => {
        const jadual = bekas.closest('.kad-data')?.querySelector('table.jadual-sistem');
        const tbody = jadual?.tBodies[0];
        const butang = bekas.querySelector('[data-muat-lagi-butang]');
        const status = bekas.querySelector('[data-muat-lagi-status]');
        const jumlah = Number(bekas.dataset.jumlah || 0);

        if (!jadual || !tbody || !butang) {
            return;
        }

        let urlSeterusnya = bekas.dataset.halamanSeterusnya || '';
        let sedangMuat = false;

        const bilanganDipapar = () =>
            tbody.querySelectorAll('tr:not(:has(> td[colspan]))').length;

        const kemaskiniPaparan = () => {
            const baki = Math.max(jumlah - bilanganDipapar(), 0);

            if (!urlSeterusnya || baki === 0) {
                butang.hidden = true;
                status.textContent = jumlah > 0 ? `Semua ${jumlah} rekod dipaparkan` : '';
                return;
            }

            butang.hidden = false;
            butang.disabled = false;
            butang.textContent = `Muat lagi (${baki} lagi)`;
            status.textContent = '';
        };

        const muatLagi = async () => {
            if (sedangMuat || !urlSeterusnya) {
                return;
            }

            sedangMuat = true;
            butang.disabled = true;
            butang.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Memuatkan...';

            try {
                const respons = await fetch(urlSeterusnya, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });

                if (!respons.ok) {
                    throw new Error(`HTTP ${respons.status}`);
                }

                const dokumen = new DOMParser().parseFromString(await respons.text(), 'text/html');
                const barisBaharu = [...(dokumen.querySelector('table.jadual-sistem')?.tBodies[0]?.rows ?? [])]
                    .filter((baris) => !baris.querySelector(':scope > td[colspan]'));

                const barisDitambah = barisBaharu.map((baris) => {
                    const salinan = document.importNode(baris, true);
                    tbody.appendChild(salinan);
                    return salinan;
                });

                sediakanKadJadual(jadual, barisDitambah);
                barisDitambah.forEach((baris) => window.mpasPasangBaris?.(baris));

                urlSeterusnya = dokumen.querySelector('[data-muat-lagi]')?.dataset.halamanSeterusnya || '';
            } catch (ralat) {
                status.textContent = 'Gagal memuatkan rekod. Sila cuba lagi.';
                console.error(ralat);
            } finally {
                sedangMuat = false;
                kemaskiniPaparan();
            }
        };

        butang.addEventListener('click', muatLagi);

        // Muat sendiri bila butang hampir kelihatan (telefon sahaja)
        if ('IntersectionObserver' in window) {
            new IntersectionObserver((entri) => {
                if (skrinTelefon.matches && entri.some((item) => item.isIntersecting)) {
                    muatLagi();
                }
            }, { rootMargin: '300px 0px' }).observe(bekas);
        }

        kemaskiniPaparan();
    });
});

// Tanda bar penapis telefon bila ia sedang melekat (untuk garisan bayang)
document.querySelectorAll('.penapis-mobile').forEach((bar) => {
    const kemaskini = () => {
        const atas = parseFloat(getComputedStyle(bar).top) || 0;
        bar.classList.toggle('sedang-melekat', bar.getBoundingClientRect().top <= atas + 1 && window.scrollY > 0);
    };

    window.addEventListener('scroll', kemaskini, { passive: true });
    kemaskini();
});

/*
|--------------------------------------------------------------------------
| Butang Back telefon menutup modal butiran (bukan keluar dari senarai)
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const skrinTelefon = window.matchMedia('(max-width: 767.98px)');

    ['#modalButiranAset', '#modalButiranPengguna'].forEach((pemilih) => {
        const modal = document.querySelector(pemilih);

        if (!modal) {
            return;
        }

        modal.addEventListener('shown.bs.modal', () => {
            if (skrinTelefon.matches && !history.state?.mpasModal) {
                history.pushState({ mpasModal: true }, '');
            }
        });

        modal.addEventListener('hidden.bs.modal', () => {
            if (history.state?.mpasModal) {
                history.back();
            }
        });
    });

    window.addEventListener('popstate', () => {
        const modalTerbuka = document.querySelector('#modalButiranAset.show, #modalButiranPengguna.show');

        if (modalTerbuka) {
            bootstrap.Modal.getInstance(modalTerbuka)?.hide();
        }
    });
});

/*
|--------------------------------------------------------------------------
| Borang aset (bangunan & tanah) gaya aplikasi telefon
|--------------------------------------------------------------------------
| Lapisan ini TIDAK menggantikan logik langkah sedia ada. Ia hanya:
| - memaparkan "Langkah X daripada Y" + bar kemajuan nipis
| - bar navigasi melekat di bawah (menekan butang asal dalam langkah aktif)
| - amaran sebelum keluar jika ada perubahan
| - papan kekunci ikut jenis medan
| - draf automatik (borang daftar sahaja)
| - bottom sheet untuk senarai pilihan (telefon)
*/
document.addEventListener('DOMContentLoaded', () => {
    const borang = document.querySelector('form[data-borang-bangunan], form[data-borang-tanah]');

    if (!borang) {
        return;
    }

    const skrinTelefon = window.matchMedia('(max-width: 767.98px)');
    const langkah = [...borang.querySelectorAll('.langkah-borang[data-langkah]')];
    const borangDaftar = !borang.querySelector('input[name="_method"]');
    let adaPerubahan = false;

    borang.classList.add('borang-apl');

    /* ---------- 1. Papan kekunci ikut jenis medan ---------- */
    borang.querySelectorAll('input[type="number"]').forEach((input) => {
        input.setAttribute('inputmode', input.step && input.step !== '1' ? 'decimal' : 'decimal');
    });

    borang.querySelectorAll('input[name*="postcode"], input[name*="poskod"], input[name*="ic_no"]').forEach((input) => {
        input.setAttribute('inputmode', 'numeric');
    });

    borang.querySelectorAll('input[name*="phone"], input[name*="telefon"]').forEach((input) => {
        input.setAttribute('inputmode', 'tel');
    });

    borang.querySelectorAll('input[type="email"], input[name*="email"]').forEach((input) => {
        input.setAttribute('inputmode', 'email');
    });

    /* ---------- 2. Kepala wizard & bar navigasi (telefon) ---------- */
    if (langkah.length > 1) {
        const kepala = document.createElement('div');
        kepala.className = 'kepala-wizard-apl d-md-none';
        kepala.innerHTML = `
            <div class="bar-kemajuan-apl"><span data-wiz-kemajuan></span></div>
            <p class="teks-langkah-apl" data-wiz-teks></p>
        `;

        const bekasLangkah = langkah[0].parentElement;
        bekasLangkah.insertBefore(kepala, langkah[0]);

        const nav = document.createElement('div');
        nav.className = 'nav-wizard-apl d-md-none';
        nav.innerHTML = `
            <button type="button" class="btn btn-light" data-wiz-sebelum><i class="bi bi-chevron-left me-1"></i>Sebelum</button>
            <button type="button" class="btn btn-utama" data-wiz-seterusnya></button>
        `;
        borang.appendChild(nav);

        // Tanda bekas butang navigasi asal supaya boleh disembunyikan di telefon
        langkah.forEach((seksyen) => {
            seksyen.querySelectorAll('[data-seterusnya], [data-sebelum], [data-sebelumnya], button[type="submit"]:not([form])').forEach((butang) => {
                butang.parentElement.classList.add('nav-langkah-asal');
            });
        });

        const langkahAktif = () => langkah.find((seksyen) => seksyen.classList.contains('aktif'));

        // Nama setiap langkah (dari penunjuk langkah desktop)
        const penunjukWizard = [...borang.querySelectorAll('[data-penunjuk-bangunan], [data-penunjuk-item]')];
        const namaLangkahWizard = penunjukWizard.map((item) => item.querySelector('strong')?.textContent.trim() ?? '');

        // Kemaskini sahaja: tekan teks langkah → bottom sheet senarai langkah untuk terus ke bahagian lain
        if (! borangDaftar && penunjukWizard.length === langkah.length) {
            const teksWizard = kepala.querySelector('[data-wiz-teks]');
            teksWizard.classList.add('boleh-tekan');
            teksWizard.setAttribute('role', 'button');
            teksWizard.setAttribute('tabindex', '0');
            teksWizard.setAttribute('aria-label', 'Pilih bahagian borang');

            const lembaranLangkah = document.createElement('div');
            lembaranLangkah.className = 'offcanvas offcanvas-bottom panel-penapis-mobile lembaran-langkah-apl';
            lembaranLangkah.tabIndex = -1;
            lembaranLangkah.innerHTML = `
                <div class="pemegang-panel" aria-hidden="true"></div>
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">Pergi ke bahagian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
                </div>
                <div class="offcanvas-body pt-0">
                    <div class="senarai-pilihan-apl" data-senarai-langkah></div>
                </div>
            `;
            document.body.appendChild(lembaranLangkah);

            const offcanvasLangkah = bootstrap.Offcanvas.getOrCreateInstance(lembaranLangkah);
            const senaraiLangkah = lembaranLangkah.querySelector('[data-senarai-langkah]');

            const bukaSenaraiLangkah = () => {
                const indeksAktif = langkah.indexOf(langkahAktif());
                senaraiLangkah.innerHTML = '';

                namaLangkahWizard.forEach((nama, indeks) => {
                    const baris = document.createElement('button');
                    baris.type = 'button';
                    baris.className = 'baris-pilihan-apl baris-langkah-apl' + (indeks === indeksAktif ? ' dipilih' : '');
                    baris.innerHTML = `
                        <span class="nombor-langkah-apl">${indeks + 1}</span>
                        <span class="flex-fill text-start"></span>
                        ${indeks === indeksAktif ? '<small class="label-semasa-apl">Semasa</small>' : ''}
                    `;
                    baris.querySelector('.flex-fill').textContent = nama;

                    baris.addEventListener('click', () => {
                        offcanvasLangkah.hide();

                        if (indeks !== indeksAktif) {
                            penunjukWizard[indeks].click();
                        }
                    });

                    senaraiLangkah.appendChild(baris);
                });

                offcanvasLangkah.show();
            };

            teksWizard.addEventListener('click', bukaSenaraiLangkah);
            teksWizard.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    bukaSenaraiLangkah();
                }
            });
        }

        const kemaskiniWizard = () => {
            const aktif = langkahAktif();

            if (!aktif) {
                return;
            }

            const indeks = langkah.indexOf(aktif);
            const akhir = indeks === langkah.length - 1;

            const teksWizard = kepala.querySelector('[data-wiz-teks]');

            teksWizard.textContent = `Langkah ${indeks + 1} daripada ${langkah.length}`;

            if (! borangDaftar) {
                teksWizard.insertAdjacentHTML('beforeend', ' <i class="bi bi-chevron-down" aria-hidden="true"></i>');
            }
            kepala.querySelector('[data-wiz-kemajuan]').style.width = `${((indeks + 1) / langkah.length) * 100}%`;

            nav.querySelector('[data-wiz-sebelum]').hidden = indeks === 0;
            nav.querySelector('[data-wiz-seterusnya]').innerHTML = akhir
                ? '<i class="bi bi-check2-circle me-1"></i>Simpan'
                : 'Seterusnya<i class="bi bi-chevron-right ms-1"></i>';
        };

        nav.querySelector('[data-wiz-seterusnya]').addEventListener('click', () => {
            const aktif = langkahAktif();
            const butang = aktif?.querySelector('[data-seterusnya]')
                ?? aktif?.querySelector('button[type="submit"]:not([form])');

            butang?.click();
        });

        nav.querySelector('[data-wiz-sebelum]').addEventListener('click', () => {
            langkahAktif()?.querySelector('[data-sebelum], [data-sebelumnya]')?.click();
        });

        // Ikut perubahan langkah aktif + tatal ke atas bila tukar langkah (telefon)
        let indeksTerakhir = -1;

        new MutationObserver(() => {
            kemaskiniWizard();

            const indeks = langkah.indexOf(langkahAktif());

            if (indeks !== -1 && indeks !== indeksTerakhir) {
                if (indeksTerakhir !== -1 && skrinTelefon.matches) {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }

                indeksTerakhir = indeks;
            }
        }).observe(bekasLangkah, { subtree: true, attributes: true, attributeFilter: ['class'] });

        kemaskiniWizard();
    }

    /* ---------- 3. Amaran sebelum keluar ---------- */
    borang.addEventListener('input', () => {
        adaPerubahan = true;
    });

    borang.addEventListener('submit', () => {
        adaPerubahan = false;
    });

    document.querySelectorAll('[data-keluar-borang]').forEach((pautan) => {
        pautan.addEventListener('click', (event) => {
            if (!adaPerubahan) {
                return;
            }

            event.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Keluar dari borang?',
                text: borangDaftar
                    ? 'Maklumat yang telah diisi disimpan sebagai draf dan boleh disambung kemudian.'
                    : 'Perubahan yang belum disimpan akan hilang.',
                showCancelButton: true,
                confirmButtonText: 'Ya, keluar',
                cancelButtonText: 'Teruskan mengisi',
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#168A7E',
                reverseButtons: true,
            }).then((hasil) => {
                if (hasil.isConfirmed) {
                    window.location.href = pautan.href;
                }
            });
        });
    });

    /* ---------- 4. Draf automatik (borang daftar sahaja) ---------- */
    if (borangDaftar) {
        const kunciDraf = `mpas-draf:${borang.getAttribute('action')}`;
        const adaRalatServer = borang.querySelector('.is-invalid, .invalid-feedback.d-block, .alert-danger');

        const baca = () => {
            try {
                return JSON.parse(localStorage.getItem(kunciDraf) || 'null');
            } catch {
                return null;
            }
        };

        const buang = () => {
            try {
                localStorage.removeItem(kunciDraf);
            } catch {
                // abaikan
            }
        };

        let pemasaDraf;

        const simpanDraf = () => {
            window.clearTimeout(pemasaDraf);

            pemasaDraf = window.setTimeout(() => {
                const data = [];

                for (const [nama, nilai] of new FormData(borang)) {
                    if (nama === '_token' || nilai instanceof File) {
                        continue;
                    }

                    data.push([nama, nilai]);
                }

                try {
                    localStorage.setItem(kunciDraf, JSON.stringify({ masa: Date.now(), data }));
                } catch {
                    // storan penuh / disekat: abaikan
                }
            }, 600);
        };

        const pulihkanDraf = (draf) => {
            // Penyewa dinamik: tambah baris secukupnya dahulu
            const indeksPenyewa = [...new Set(draf.data
                .map(([nama]) => nama.match(/^penyewa\[(\d+)\]/)?.[1])
                .filter((nilai) => nilai !== undefined))];

            const butangTambah = borang.querySelector('[data-tambah-penyewa]');
            const barisSedia = () => [...borang.querySelectorAll('[name^="penyewa["][name$="[tnt_name]"]')];

            while (butangTambah && barisSedia().length < indeksPenyewa.length) {
                butangTambah.click();
            }

            const petaIndeks = {};
            barisSedia().forEach((input, kedudukan) => {
                const indeksBaharu = input.name.match(/^penyewa\[(\d+)\]/)?.[1];

                if (indeksPenyewa[kedudukan] !== undefined) {
                    petaIndeks[indeksPenyewa[kedudukan]] = indeksBaharu;
                }
            });

            const nilaiIkutNama = {};

            draf.data.forEach(([nama, nilai]) => {
                const namaSebenar = nama.replace(/^penyewa\[(\d+)\]/, (padanan, i) => `penyewa[${petaIndeks[i] ?? i}]`);
                (nilaiIkutNama[namaSebenar] ??= []).push(nilai);
            });

            Object.entries(nilaiIkutNama).forEach(([nama, senaraiNilai]) => {
                const medan = [...borang.querySelectorAll(`[name="${CSS.escape(nama)}"]`)];

                medan.forEach((item) => {
                    if (item.type === 'file' || item.type === 'hidden' && nama === '_token') {
                        return;
                    }

                    if (item.type === 'checkbox' || item.type === 'radio') {
                        item.checked = senaraiNilai.includes(item.value);
                    } else if (item.type !== 'hidden') {
                        item.value = senaraiNilai[0] ?? '';
                    }

                    item.dispatchEvent(new Event('input', { bubbles: true }));
                    item.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });

            adaPerubahan = true;
        };

        const draf = baca();

        if (draf && adaRalatServer) {
            // Data lama dari server (old()) diutamakan
        } else if (draf && Array.isArray(draf.data) && draf.data.length) {
            const masa = new Date(draf.masa).toLocaleString('ms-MY', {
                day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit',
            });

            Swal.fire({
                icon: 'info',
                title: 'Draf ditemui',
                html: `Terdapat maklumat yang belum dihantar (disimpan ${masa}).<br><small class="text-secondary">Fail dokumen perlu dimuat naik semula.</small>`,
                showCancelButton: true,
                confirmButtonText: 'Sambung draf',
                cancelButtonText: 'Mula baharu',
                confirmButtonColor: '#168A7E',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
            }).then((hasil) => {
                if (hasil.isConfirmed) {
                    pulihkanDraf(draf);
                } else if (hasil.dismiss === Swal.DismissReason.cancel) {
                    buang();
                }
            });
        }

        borang.addEventListener('input', simpanDraf);
        borang.addEventListener('change', simpanDraf);
        borang.addEventListener('submit', () => {
            window.clearTimeout(pemasaDraf);
            buang();
        });
    }

    /* ---------- 5. Bottom sheet untuk senarai pilihan (telefon) ---------- */
    const panel = document.createElement('div');
    panel.className = 'offcanvas offcanvas-bottom panel-penapis-mobile';
    panel.tabIndex = -1;
    panel.innerHTML = `
        <div class="pemegang-panel" aria-hidden="true"></div>
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" data-pemilih-tajuk></h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body pt-0">
            <div class="senarai-pilihan-apl" role="listbox" data-pemilih-senarai></div>
        </div>
    `;
    document.body.appendChild(panel);

    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(panel);
    const tajukPanel = panel.querySelector('[data-pemilih-tajuk]');
    const senaraiPanel = panel.querySelector('[data-pemilih-senarai]');

    const labelMedan = (select) =>
        (select.id && borang.querySelector(`label[for="${CSS.escape(select.id)}"]`)?.innerText)
        || select.closest('[class*="col"], .mb-3')?.querySelector('label')?.innerText
        || 'Pilih';

    const bukaPemilih = (select) => {
        tajukPanel.textContent = labelMedan(select).replace('*', '').trim();
        senaraiPanel.innerHTML = '';

        [...select.options].forEach((pilihan) => {
            if (pilihan.disabled) {
                return;
            }

            const butang = document.createElement('button');
            butang.type = 'button';
            butang.className = 'baris-pilihan-apl' + (pilihan.selected ? ' dipilih' : '');
            butang.setAttribute('role', 'option');
            butang.setAttribute('aria-selected', pilihan.selected ? 'true' : 'false');
            butang.innerHTML = `<span></span><i class="bi bi-check-lg" aria-hidden="true"></i>`;
            butang.querySelector('span').textContent = pilihan.textContent.trim();

            butang.addEventListener('click', () => {
                select.value = pilihan.value;
                select.dispatchEvent(new Event('input', { bubbles: true }));
                select.dispatchEvent(new Event('change', { bubbles: true }));
                offcanvas.hide();
            });

            senaraiPanel.appendChild(butang);
        });

        offcanvas.show();
    };

    borang.addEventListener('mousedown', (event) => {
        const select = event.target.closest('select:not([multiple])');

        if (!select || !skrinTelefon.matches || select.disabled) {
            return;
        }

        event.preventDefault();
        bukaPemilih(select);
    });

    borang.addEventListener('keydown', (event) => {
        const select = event.target.closest?.('select:not([multiple])');

        if (!select || !skrinTelefon.matches || !['Enter', ' '].includes(event.key)) {
            return;
        }

        event.preventDefault();
        bukaPemilih(select);
    });
});

/*
|--------------------------------------------------------------------------
| Pemeriksaan penyelenggaraan: ringkasan kemajuan melekat (telefon)
|--------------------------------------------------------------------------
| Membaca nilai daripada blok kemajuan sedia ada; tiada logik kiraan baharu.
*/
document.addEventListener('DOMContentLoaded', () => {
    const kemajuan = document.querySelector('[data-kemajuan-pemeriksaan]');
    const komponenPertama = document.querySelector('[data-komponen-pemeriksaan]');

    if (!kemajuan || !komponenPertama) {
        return;
    }

    const melekat = document.createElement('div');
    melekat.className = 'kemajuan-melekat-apl d-md-none';
    melekat.setAttribute('aria-hidden', 'true');
    melekat.innerHTML = `
        <div class="d-flex justify-content-between align-items-center gap-2">
            <span><strong data-m-diisi>0</strong> / <span data-m-jumlah>0</span> diisi</span>
            <span class="d-flex gap-1">
                <span class="kiraan-nilai baik"><span data-m-bil="BAIK">0</span></span>
                <span class="kiraan-nilai rosak"><span data-m-bil="ROSAK">0</span></span>
                <span class="kiraan-nilai tb"><span data-m-bil="TIDAK_BERKAITAN">0</span></span>
            </span>
        </div>
        <div class="bar-kemajuan-apl mt-2"><span data-m-bar></span></div>
    `;

    komponenPertama.parentElement.insertBefore(melekat, komponenPertama);

    const jumlah = Number((kemajuan.textContent.match(/\/\s*(\d+)/) || [])[1] || 0);

    const salin = () => {
        melekat.querySelector('[data-m-diisi]').textContent =
            kemajuan.querySelector('[data-bil-diisi]')?.textContent ?? '0';
        melekat.querySelector('[data-m-jumlah]').textContent = jumlah;

        ['BAIK', 'ROSAK', 'TIDAK_BERKAITAN'].forEach((nilai) => {
            melekat.querySelector(`[data-m-bil="${nilai}"]`).textContent =
                kemajuan.querySelector(`[data-bil="${nilai}"]`)?.textContent ?? '0';
        });

        melekat.querySelector('[data-m-bar]').style.width =
            kemajuan.querySelector('[data-bar-kemajuan]')?.style.width || '0%';
    };

    new MutationObserver(salin).observe(kemajuan, {
        subtree: true,
        childList: true,
        characterData: true,
        attributes: true,
        attributeFilter: ['style'],
    });

    salin();
});

/*
|--------------------------------------------------------------------------
| Butiran aduan (telefon): lembaran tindakan, terima cepat, salin teks
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const skrinTelefon = window.matchMedia('(max-width: 767.98px)');
    const lembaran = document.querySelector('#lembaranTindakanAduan');

    // Salin teks ringkas (contoh: no. rujukan aduan)
    document.querySelectorAll('[data-salin-teks]').forEach((butang) => {
        butang.addEventListener('click', async () => {
            const teks = butang.dataset.salinTeks;
            let berjaya = false;

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(teks);
                    berjaya = true;
                } else {
                    const medan = document.createElement('textarea');
                    medan.value = teks;
                    medan.style.position = 'fixed';
                    medan.style.top = '-9999px';
                    document.body.appendChild(medan);
                    medan.select();
                    berjaya = document.execCommand('copy');
                    medan.remove();
                }
            } catch {
                berjaya = false;
            }

            Swal.fire({
                icon: berjaya ? 'success' : 'error',
                title: berjaya ? 'Disalin' : 'Tidak dapat disalin',
                text: berjaya ? teks : 'Sila salin secara manual.',
                confirmButtonColor: '#168A7E',
                timer: berjaya ? 1400 : undefined,
            });
        });
    });

    // Terima cepat: hantar borang terima sedia ada (pengesahan SweetAlert kekal)
    document.querySelector('[data-terima-cepat]')?.addEventListener('click', () => {
        document.querySelector('[data-terima-aduan]')?.requestSubmit();
    });

    if (!lembaran) {
        return;
    }

    // Pindahkan lembaran ke <body> di telefon supaya kedudukan fixed tidak terperangkap
    const latar = document.createElement('div');
    latar.className = 'latar-lembaran-apl';
    document.body.appendChild(latar);

    if (skrinTelefon.matches) {
        document.body.appendChild(lembaran);
    }

    const buka = (pemilihFokus) => {
        lembaran.classList.add('terbuka');
        latar.classList.add('terbuka');
        document.body.style.overflow = 'hidden';

        if (pemilihFokus) {
            window.setTimeout(() => lembaran.querySelector(pemilihFokus)?.focus(), 300);
        }
    };

    const tutup = () => {
        lembaran.classList.remove('terbuka');
        latar.classList.remove('terbuka');
        document.body.style.overflow = '';
    };

    document.querySelectorAll('[data-buka-lembaran]').forEach((butang) => {
        butang.addEventListener('click', () => buka(butang.dataset.fokus));
    });

    lembaran.querySelectorAll('[data-tutup-lembaran]').forEach((butang) => {
        butang.addEventListener('click', tutup);
    });

    latar.addEventListener('click', tutup);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && lembaran.classList.contains('terbuka')) {
            tutup();
        }
    });

    // Ralat validasi dari server: buka semula lembaran supaya mesej ralat kelihatan
    if (skrinTelefon.matches && lembaran.querySelector('.is-invalid, .invalid-feedback.d-block, .text-danger.small')) {
        buka();
    }
});

/*
|--------------------------------------------------------------------------
| Lightbox gambar aduan (telefon): leret, ketik dua kali zum, butang Back
|--------------------------------------------------------------------------
| Menggunakan butang sebelum/seterusnya sedia ada; logik paparan tidak diubah.
*/
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.querySelector('#modalLightboxAduan');

    if (!modal) {
        return;
    }

    const skrinTelefon = window.matchMedia('(max-width: 767.98px)');
    const ruang = modal.querySelector('.ruang-lightbox-aduan');
    const figura = modal.querySelector('.gambar-utama-lightbox');
    const gambar = modal.querySelector('[data-lightbox-gambar]');
    const butangSebelum = modal.querySelector('[data-lightbox-sebelum]');
    const butangSeterusnya = modal.querySelector('[data-lightbox-seterusnya]');

    const nyahZum = () => figura?.classList.remove('dizum');

    // Tukar gambar = keluar dari mod zum
    new MutationObserver(nyahZum).observe(gambar, { attributes: true, attributeFilter: ['src'] });

    // Leret kiri / kanan
    let mulaX = 0;
    let mulaY = 0;

    ruang?.addEventListener('touchstart', (event) => {
        mulaX = event.touches[0].clientX;
        mulaY = event.touches[0].clientY;
    }, { passive: true });

    ruang?.addEventListener('touchend', (event) => {
        if (!skrinTelefon.matches || figura?.classList.contains('dizum')) {
            return;
        }

        const beza = event.changedTouches[0].clientX - mulaX;
        const bezaY = event.changedTouches[0].clientY - mulaY;

        if (Math.abs(beza) < 50 || Math.abs(beza) < Math.abs(bezaY)) {
            return;
        }

        (beza < 0 ? butangSeterusnya : butangSebelum)?.click();
    });

    // Ketik dua kali untuk zum 2x
    let ketikTerakhir = 0;

    gambar.addEventListener('click', (event) => {
        if (!skrinTelefon.matches) {
            return;
        }

        const sekarang = Date.now();

        if (sekarang - ketikTerakhir < 300) {
            const zum = !figura.classList.contains('dizum');
            figura.classList.toggle('dizum', zum);

            if (zum) {
                // Fokus pada titik yang diketik
                const kotak = figura.getBoundingClientRect();
                const nisbahX = (event.clientX - kotak.left) / kotak.width;
                const nisbahY = (event.clientY - kotak.top) / kotak.height;

                requestAnimationFrame(() => {
                    figura.scrollLeft = figura.scrollWidth * nisbahX - kotak.width / 2;
                    figura.scrollTop = figura.scrollHeight * nisbahY - kotak.height / 2;
                });
            }

            ketikTerakhir = 0;
            return;
        }

        ketikTerakhir = sekarang;
    });

    // Butang Back telefon menutup lightbox
    modal.addEventListener('shown.bs.modal', () => {
        if (skrinTelefon.matches && !history.state?.mpasLightbox) {
            history.pushState({ mpasLightbox: true }, '');
        }
    });

    modal.addEventListener('hidden.bs.modal', () => {
        nyahZum();

        if (history.state?.mpasLightbox) {
            history.back();
        }
    });

    window.addEventListener('popstate', () => {
        if (modal.classList.contains('show')) {
            bootstrap.Modal.getInstance(modal)?.hide();
        }
    });
});

/*
|--------------------------------------------------------------------------
| Tetapan: bar perubahan belum disimpan, stepper, pratonton rujukan,
| suis status terus dari senarai
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const toastTetapan = (icon, title) => {
        if (typeof Swal === 'undefined') {
            return;
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });
    };

    /* ---------- Stepper − / + untuk medan nombor ---------- */
    document.querySelectorAll('input[type="number"][data-stepper]').forEach((input) => {
        const kumpulan = input.closest('.input-group');

        if (! kumpulan) {
            return;
        }

        const buatButang = (teks, label, arah) => {
            const butang = document.createElement('button');
            butang.type = 'button';
            butang.className = 'btn btn-outline-secondary butang-stepper';
            butang.setAttribute('aria-label', label);
            butang.innerHTML = teks;
            butang.addEventListener('click', () => {
                if (input.value === '') {
                    input.value = input.min || 0;
                }

                arah > 0 ? input.stepUp() : input.stepDown();
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            return butang;
        };

        kumpulan.classList.add('kumpulan-stepper');
        kumpulan.insertBefore(buatButang('<i class="bi bi-dash-lg"></i>', 'Kurangkan', -1), input);
        input.after(buatButang('<i class="bi bi-plus-lg"></i>', 'Tambah', 1));
    });

    /* ---------- Pratonton nombor rujukan seterusnya ---------- */
    document.querySelectorAll('[data-pratonton-rujukan]').forEach((kotak) => {
        const jenis = kotak.dataset.pratontonRujukan;
        const awalan = document.getElementById(`${jenis}_awalan`);
        const mula = document.getElementById(`${jenis}_nombor_mula`);
        const panjang = document.getElementById(`${jenis}_panjang_nombor`);
        const teks = kotak.querySelector('[data-pratonton-teks]');
        const semasa = kotak.dataset.nomborSemasa;

        if (! awalan || ! mula || ! panjang || ! teks) {
            return;
        }

        const kemaskini = () => {
            const nombor = semasa !== '' && semasa !== undefined
                ? parseInt(semasa, 10) + 1
                : Math.max(parseInt(mula.value, 10) || 1, 1);
            const lebar = Math.min(Math.max(parseInt(panjang.value, 10) || 1, 1), 10);

            teks.textContent = `${awalan.value.trim() || '…'}(${String(nombor).padStart(lebar, '0')})`;

            document.querySelectorAll(`[data-pratonton-mobile="${jenis}"]`).forEach((sasaran) => {
                sasaran.textContent = teks.textContent;
            });
        };

        [awalan, mula, panjang].forEach((medan) => {
            medan.addEventListener('input', kemaskini);
            medan.addEventListener('change', kemaskini);
        });

        kemaskini();
    });

    /* ---------- Bar "perubahan belum disimpan" ---------- */
    document.querySelectorAll('form[data-pantau-ubah]').forEach((borang) => {
        const ambilData = () => new URLSearchParams(new FormData(borang)).toString();
        let dataAsal = null;
        let sedangHantar = false;

        const bar = document.createElement('div');
        bar.className = 'bar-perubahan-tetapan';
        bar.setAttribute('role', 'status');
        bar.innerHTML = `
            <span class="teks-perubahan"><i class="bi bi-pencil-square me-2"></i>Ada perubahan belum disimpan</span>
            <div class="d-flex gap-2 ms-auto">
                <button type="button" class="btn btn-light" data-batal-perubahan>Batal</button>
                <button type="submit" class="btn btn-utama"><i class="bi bi-check-lg me-1"></i>Simpan</button>
            </div>`;

        const modTelefon = borang.dataset.pantauUbah === 'telefon';
        const skrinTelefonPantau = window.matchMedia('(max-width: 767.98px)');
        const pantauAktif = () => ! modTelefon || skrinTelefonPantau.matches;

        if (modTelefon) {
            bar.classList.add('d-md-none');
        } else {
            borang.classList.add('ada-bar-perubahan');
        }

        borang.appendChild(bar);

        const berubah = () => dataAsal !== null && ambilData() !== dataAsal;

        const semak = () => {
            const ada = berubah();
            bar.classList.toggle('kelihatan', ada);

            if (borang.id) {
                document.querySelectorAll(`[data-simpan-bar="${borang.id}"]`).forEach((butang) => {
                    butang.disabled = ! ada;
                });
            }

            document.body.classList.toggle('ada-perubahan-tetapan', ada);
        };

        // Ambil keadaan asal selepas skrip halaman lain selesai
        setTimeout(() => {
            dataAsal = ambilData();
            semak();
        }, 0);

        borang.addEventListener('input', semak);
        borang.addEventListener('change', semak);

        bar.querySelector('[data-batal-perubahan]').addEventListener('click', () => {
            borang.reset();
            borang.querySelectorAll('input, select, textarea').forEach((medan) => {
                medan.dispatchEvent(new Event('change', { bubbles: true }));
            });
            semak();
        });

        borang.addEventListener('submit', () => {
            sedangHantar = true;
        });

        window.addEventListener('beforeunload', (event) => {
            if (! sedangHantar && pantauAktif() && berubah()) {
                event.preventDefault();
                event.returnValue = '';
            }
        });

        // Amaran mesra apabila tekan pautan lain (termasuk ← pada bar aplikasi)
        document.addEventListener('click', (event) => {
            const pautan = event.target.closest('a[href]');

            if (! pautan || sedangHantar || ! pantauAktif() || ! berubah()) {
                return;
            }

            const href = pautan.getAttribute('href');

            if (! href || href.startsWith('#') || href.startsWith('javascript:') || pautan.target === '_blank' || pautan.hasAttribute('data-bs-toggle') || pautan.hasAttribute('download')) {
                return;
            }

            if (typeof Swal === 'undefined') {
                return;
            }

            event.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Buang perubahan?',
                text: 'Perubahan yang belum disimpan akan hilang.',
                showCancelButton: true,
                confirmButtonText: 'Buang',
                cancelButtonText: 'Terus edit',
                reverseButtons: true,
            }).then((hasil) => {
                if (hasil.isConfirmed) {
                    sedangHantar = true;
                    window.location.href = pautan.href;
                }
            });
        }, true);
    });

    /* ---------- Suis aktif / tidak aktif terus dari senarai ---------- */
    document.querySelectorAll('input[data-togol-status]').forEach((suis) => {
        suis.addEventListener('change', async () => {
            const aktif = suis.checked;
            const bekas = suis.closest('.kad-komponen, tr, [data-baris-togol]');
            const lencana = bekas?.querySelector('[data-lencana-status]');
            const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            suis.disabled = true;

            try {
                const respons = await fetch(suis.dataset.togolStatus, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ aktif }),
                });

                if (! respons.ok) {
                    throw new Error(`HTTP ${respons.status}`);
                }

                const data = await respons.json();

                // Selaraskan suis status dalam modal ubah supaya simpanan modal tidak memulihkan status lama
                const modalSasaran = suis.dataset.modalSasaran
                    ? document.querySelector(suis.dataset.modalSasaran)
                    : null;

                modalSasaran?.querySelectorAll('input[type="checkbox"][name="amc_status"], input[type="checkbox"][name="pgw_status"], input[type="checkbox"][name="tap_status"], input[type="checkbox"][name="sts_status"]').forEach((suisModal) => {
                    suisModal.checked = aktif;
                    suisModal.defaultChecked = aktif;
                });

                document.querySelectorAll(`input[data-togol-status="${suis.dataset.togolStatus}"]`).forEach((suisLain) => {
                    if (suisLain !== suis) {
                        suisLain.checked = aktif;
                        suisLain.closest('.kad-komponen, tr, [data-baris-togol]')?.classList.toggle(
                            suisLain.closest('tr') ? 'opacity-75' : 'tidak-aktif',
                            ! aktif
                        );
                    }
                });

                if (bekas?.classList.contains('kad-komponen') || bekas?.hasAttribute('data-baris-togol')) {
                    bekas.classList.toggle('tidak-aktif', ! aktif);
                } else {
                    bekas?.classList.toggle('opacity-75', ! aktif);
                }

                if (lencana) {
                    lencana.textContent = data.label;
                    lencana.classList.toggle('utama', aktif);
                    lencana.classList.toggle('kelabu', ! aktif);
                }

                toastTetapan('success', data.mesej);
            } catch (ralat) {
                suis.checked = ! aktif;
                toastTetapan('error', 'Status gagal dikemas kini. Sila cuba lagi.');
            } finally {
                suis.disabled = false;
            }
        });
    });
});

/*
|--------------------------------------------------------------------------
| Tetapan (telefon): senarai gaya Settings
| - [data-nilai-medan]  : papar nilai medan sebenar
| - [data-sunting-medan]: tekan baris → bottom sheet ubah satu medan
| - [data-stepper-proksi]: − / + pada medan nombor sebenar
| - [data-proksi-suis]  : suis matriks yang mengawal suis sebenar
| - select[data-segmen] : pilihan segmen (telefon)
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const hantarPeristiwa = (medan) => {
        medan.dispatchEvent(new Event('input', { bubbles: true }));
        medan.dispatchEvent(new Event('change', { bubbles: true }));
    };

    /* ---------- Nilai medan ---------- */
    const segarkanNilai = () => {
        document.querySelectorAll('[data-nilai-medan]').forEach((paparan) => {
            const medan = document.getElementById(paparan.dataset.nilaiMedan);
            let teks = '';

            if (medan) {
                teks = medan.tagName === 'SELECT'
                    ? (medan.value !== '' ? medan.selectedOptions[0]?.textContent.trim() ?? '' : '')
                    : medan.value;
            }

            paparan.textContent = teks || paparan.dataset.kosong || '—';
            paparan.classList.toggle('kosong', ! teks);
        });
    };

    if (document.querySelector('[data-nilai-medan]')) {
        segarkanNilai();
        document.addEventListener('input', segarkanNilai);
        document.addEventListener('change', segarkanNilai);
    }

    /* ---------- Stepper proksi ---------- */
    document.querySelectorAll('[data-stepper-proksi]').forEach((stepper) => {
        const medan = document.getElementById(stepper.dataset.stepperProksi);

        if (! medan) {
            return;
        }

        stepper.querySelectorAll('[data-arah]').forEach((butang) => {
            butang.addEventListener('click', () => {
                if (medan.value === '') {
                    medan.value = medan.min || 1;
                }

                Number(butang.dataset.arah) > 0 ? medan.stepUp() : medan.stepDown();
                hantarPeristiwa(medan);
            });
        });
    });

    /* ---------- Bottom sheet ubah satu medan ---------- */
    const lembaran = document.getElementById('lembaranSuntingMedan');

    if (lembaran && typeof bootstrap !== 'undefined') {
        const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(lembaran);
        const tajuk = lembaran.querySelector('[data-tajuk-sunting]');
        const input = lembaran.querySelector('[data-input-sunting]');
        const kotakPratonton = lembaran.querySelector('[data-pratonton-sunting]');
        let medanAktif = null;
        let nilaiAsal = '';
        let disimpan = false;

        const kemaskiniPratonton = () => {
            const jenis = medanAktif?.id.split('_')[0];
            const sumber = jenis ? document.querySelector(`[data-pratonton-mobile="${jenis}"]`) : null;

            kotakPratonton.classList.toggle('d-none', ! sumber);

            if (sumber) {
                kotakPratonton.querySelector('strong').textContent = sumber.textContent;
            }
        };

        document.querySelectorAll('[data-sunting-medan]').forEach((baris) => {
            baris.addEventListener('click', () => {
                medanAktif = document.getElementById(baris.dataset.suntingMedan);

                if (! medanAktif) {
                    return;
                }

                nilaiAsal = medanAktif.value;
                disimpan = false;

                tajuk.textContent = baris.dataset.label || 'Ubah';
                input.type = medanAktif.type === 'number' ? 'number' : 'text';
                input.inputMode = medanAktif.type === 'number' ? 'numeric' : 'text';
                ['min', 'max', 'maxlength', 'placeholder'].forEach((atribut) => {
                    medanAktif.hasAttribute(atribut)
                        ? input.setAttribute(atribut, medanAktif.getAttribute(atribut))
                        : input.removeAttribute(atribut);
                });
                input.value = nilaiAsal;

                kemaskiniPratonton();
                offcanvas.show();
            });
        });

        // Tulis terus ke medan sebenar supaya pratonton berubah semasa menaip
        input.addEventListener('input', () => {
            if (! medanAktif) {
                return;
            }

            medanAktif.value = input.value;
            medanAktif.dispatchEvent(new Event('input', { bubbles: true }));
            kemaskiniPratonton();
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                lembaran.querySelector('[data-simpan-sunting]').click();
            }
        });

        lembaran.addEventListener('shown.bs.offcanvas', () => {
            input.focus();
            input.select?.();
        });

        lembaran.querySelector('[data-simpan-sunting]').addEventListener('click', () => {
            if (medanAktif) {
                disimpan = true;
                hantarPeristiwa(medanAktif);
            }

            offcanvas.hide();
        });

        // Tutup tanpa tekan Selesai → kembalikan nilai asal
        lembaran.addEventListener('hidden.bs.offcanvas', () => {
            if (medanAktif && ! disimpan) {
                medanAktif.value = nilaiAsal;
                hantarPeristiwa(medanAktif);
            }

            medanAktif = null;
        });
    }

    /* ---------- Suis proksi (matriks akses) ---------- */
    const senaraiProksi = document.querySelectorAll('[data-proksi-suis]');

    if (senaraiProksi.length) {
        const segarkanProksi = () => {
            senaraiProksi.forEach((proksi) => {
                const sebenar = document.getElementById(proksi.dataset.proksiSuis);

                if (sebenar) {
                    proksi.checked = sebenar.checked;
                    proksi.classList.toggle('dikunci', sebenar.classList.contains('dikunci'));
                }
            });
        };

        senaraiProksi.forEach((proksi) => {
            proksi.addEventListener('change', () => {
                const sebenar = document.getElementById(proksi.dataset.proksiSuis);

                if (! sebenar) {
                    return;
                }

                sebenar.checked = proksi.checked;
                sebenar.dispatchEvent(new Event('change', { bubbles: true }));
                segarkanProksi();
            });
        });

        document.addEventListener('change', (event) => {
            if (! event.target.matches?.('[data-proksi-suis]')) {
                segarkanProksi();
            }
        });

        setTimeout(segarkanProksi, 0);
    }

    /* ---------- Pilihan segmen ---------- */
    document.querySelectorAll('select[data-segmen]').forEach((select) => {
        const segmen = document.createElement('div');
        segmen.className = 'segmen-apl d-md-none';
        segmen.setAttribute('role', 'radiogroup');

        const segarkan = () => {
            segmen.querySelectorAll('button').forEach((butang) => {
                const aktif = butang.dataset.nilai === select.value;
                butang.classList.toggle('aktif', aktif);
                butang.setAttribute('aria-checked', aktif ? 'true' : 'false');
            });
        };

        Array.from(select.options)
            .filter((pilihan) => pilihan.value !== '')
            .forEach((pilihan) => {
                const butang = document.createElement('button');
                butang.type = 'button';
                butang.setAttribute('role', 'radio');
                butang.dataset.nilai = pilihan.value;
                butang.textContent = pilihan.textContent.trim();
                butang.addEventListener('click', () => {
                    select.value = pilihan.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    segarkan();
                });
                segmen.appendChild(butang);
            });

        select.classList.add('d-none', 'd-md-block');
        select.after(segmen);
        select.addEventListener('change', segarkan);
        select.closest('form')?.addEventListener('reset', () => setTimeout(segarkan, 0));
        segarkan();
    });
});

/*
|--------------------------------------------------------------------------
| Borang pengguna gaya aplikasi (telefon)
| - [data-pilih-untuk]       : pilihan dalam bottom sheet → select sebenar
| - [data-suis-select]       : suis ↔ select ACTIVE / INACTIVE
| - [data-lembaran-kata-laluan]: bottom sheet kata laluan → medan sebenar
| - form[data-borang-apl]    : pengesahan mesra (medan sebenar tersembunyi)
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const skrinTelefon = window.matchMedia('(max-width: 767.98px)');
    const hantarPeristiwa = (medan) => {
        medan.dispatchEvent(new Event('input', { bubbles: true }));
        medan.dispatchEvent(new Event('change', { bubbles: true }));
    };

    /* ---------- Pilihan senarai (contoh: peranan) ---------- */
    const butangPilihan = document.querySelectorAll('[data-pilih-untuk]');

    if (butangPilihan.length) {
        const segarkanPilihan = () => {
            butangPilihan.forEach((butang) => {
                const select = document.getElementById(butang.dataset.pilihUntuk);
                const terpilih = select && select.value === butang.dataset.nilai;
                butang.classList.toggle('terpilih', terpilih);
                butang.setAttribute('aria-pressed', terpilih ? 'true' : 'false');
            });
        };

        butangPilihan.forEach((butang) => {
            butang.addEventListener('click', () => {
                const select = document.getElementById(butang.dataset.pilihUntuk);

                if (! select) {
                    return;
                }

                select.value = butang.dataset.nilai;
                hantarPeristiwa(select);
                segarkanPilihan();

                const lembaran = butang.closest('.offcanvas');

                if (lembaran && typeof bootstrap !== 'undefined') {
                    bootstrap.Offcanvas.getOrCreateInstance(lembaran).hide();
                }
            });
        });

        document.addEventListener('change', segarkanPilihan);
        segarkanPilihan();
    }

    /* ---------- Suis ↔ select status ---------- */
    document.querySelectorAll('input[data-suis-select]').forEach((suis) => {
        const select = document.getElementById(suis.dataset.suisSelect);

        if (! select) {
            return;
        }

        suis.checked = select.value === 'ACTIVE';

        suis.addEventListener('change', () => {
            select.value = suis.checked ? 'ACTIVE' : 'INACTIVE';
            hantarPeristiwa(select);
        });

        select.addEventListener('change', () => {
            suis.checked = select.value === 'ACTIVE';
        });
    });

    /* ---------- Bottom sheet kata laluan ---------- */
    document.querySelectorAll('[data-lembaran-kata-laluan]').forEach((lembaran) => {
        const sasaran = document.getElementById(lembaran.dataset.sasaran);
        const sasaranSahkan = document.getElementById(lembaran.dataset.sasaranSahkan);
        const baru = lembaran.querySelector('[data-kl-baru]');
        const sahkan = lembaran.querySelector('[data-kl-sahkan]');
        const petunjuk = lembaran.querySelector('[data-kl-petunjuk]');
        const semasa = lembaran.querySelector('[data-kl-semasa]');
        const sasaranSemasa = lembaran.dataset.sasaranSemasa
            ? document.getElementById(lembaran.dataset.sasaranSemasa)
            : null;
        const wajib = lembaran.dataset.wajib === '1';
        const teksAsal = petunjuk.textContent;

        if (! sasaran || ! sasaranSahkan) {
            return;
        }

        const segarkanBaris = () => {
            document.querySelectorAll('[data-status-kata-laluan]').forEach((paparan) => {
                const ada = sasaran.value !== '';
                paparan.textContent = ada ? paparan.dataset.ada : paparan.dataset.kosong;
                paparan.classList.toggle('kosong', ! ada);
            });
        };

        const tunjukPetunjuk = (teks, jenis = '') => {
            petunjuk.textContent = teks;
            petunjuk.classList.remove('betul', 'salah');

            if (jenis) {
                petunjuk.classList.add(jenis);
            }
        };

        const semakPadanan = () => {
            if (baru.value === '' && sahkan.value === '') {
                tunjukPetunjuk(teksAsal);
            } else if (sahkan.value === '') {
                tunjukPetunjuk(baru.value.length < 8 ? 'Sekurang-kurangnya 8 aksara.' : teksAsal, baru.value.length < 8 ? 'salah' : '');
            } else if (baru.value === sahkan.value) {
                tunjukPetunjuk('✓ Kata laluan sepadan', 'betul');
            } else {
                tunjukPetunjuk('✗ Kata laluan tidak sepadan', 'salah');
            }
        };

        const tutup = () => {
            if (typeof bootstrap !== 'undefined') {
                bootstrap.Offcanvas.getOrCreateInstance(lembaran).hide();
            }
        };

        const tulis = (nilai) => {
            if (sasaranSemasa) {
                sasaranSemasa.value = nilai ? semasa.value : '';
                hantarPeristiwa(sasaranSemasa);
            }

            sasaran.value = nilai;
            sasaranSahkan.value = nilai;
            hantarPeristiwa(sasaran);
            hantarPeristiwa(sasaranSahkan);
            segarkanBaris();
        };

        lembaran.addEventListener('show.bs.offcanvas', () => {
            baru.value = sasaran.value;
            sahkan.value = sasaranSahkan.value;

            if (semasa && sasaranSemasa) {
                semasa.value = sasaranSemasa.value;
            }

            semakPadanan();
        });

        lembaran.addEventListener('shown.bs.offcanvas', () => (semasa ?? baru).focus());

        [baru, sahkan].forEach((medan) => medan.addEventListener('input', semakPadanan));

        lembaran.querySelector('[data-kl-simpan]').addEventListener('click', () => {
            if (baru.value === '' && sahkan.value === '') {
                if (wajib) {
                    tunjukPetunjuk('Sila masukkan kata laluan.', 'salah');
                    baru.focus();

                    return;
                }

                tulis('');
                tutup();

                return;
            }

            if (semasa && semasa.value === '') {
                tunjukPetunjuk('Sila masukkan kata laluan semasa.', 'salah');
                semasa.focus();

                return;
            }

            if (baru.value.length < 8) {
                tunjukPetunjuk('Sekurang-kurangnya 8 aksara.', 'salah');
                baru.focus();

                return;
            }

            if (baru.value !== sahkan.value) {
                tunjukPetunjuk('✗ Kata laluan tidak sepadan', 'salah');
                sahkan.focus();

                return;
            }

            tulis(baru.value);
            tutup();
        });

        lembaran.querySelector('[data-kl-kosongkan]')?.addEventListener('click', () => {
            tulis('');
            tutup();
        });

        segarkanBaris();
    });

    /* ---------- Pengesahan mesra (medan sebenar tersembunyi di telefon) ---------- */
    document.querySelectorAll('form[data-borang-apl]').forEach((borang) => {
        const tetapkanMod = () => {
            borang.noValidate = skrinTelefon.matches;
        };

        tetapkanMod();
        skrinTelefon.addEventListener?.('change', tetapkanMod);

        borang.addEventListener('submit', (event) => {
            if (! skrinTelefon.matches || borang.checkValidity()) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            const tidakSah = borang.querySelector(':invalid');
            const label = tidakSah?.id
                ? borang.querySelector(`label[for="${tidakSah.id}"]`)?.textContent.replace('*', '').replace(/\s+/g, ' ').trim()
                : '';

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maklumat belum lengkap',
                    text: label ? `Sila lengkapkan: ${label}.` : 'Sila lengkapkan semua maklumat wajib.',
                    confirmButtonText: 'OK',
                });
            }
        }, true);
    });
});

/* Sahkan log keluar (butang Log keluar dalam profil telefon) */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-sahkan-keluar]').forEach((borang) => {
        borang.addEventListener('submit', (event) => {
            if (borang.dataset.disahkan === '1' || typeof Swal === 'undefined') {
                return;
            }

            event.preventDefault();

            Swal.fire({
                icon: 'question',
                title: 'Log keluar?',
                text: 'Anda perlu log masuk semula untuk menggunakan sistem.',
                showCancelButton: true,
                confirmButtonText: 'Log keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((hasil) => {
                if (hasil.isConfirmed) {
                    borang.dataset.disahkan = '1';
                    borang.submit();
                }
            });
        });
    });
});

/*
|--------------------------------------------------------------------------
| Pemilih lokasi (latitud / longitud) dengan peta
| Butang: [data-pilih-lokasi data-lat="#id" data-lng="#id"]
| - Klik peta / seret pin
| - Guna Lokasi Saya (perlukan HTTPS atau localhost)
| - Suis Jalan / Satelit
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', () => {
    const butangPilih = document.querySelectorAll('[data-pilih-lokasi]');

    if (! butangPilih.length || typeof L === 'undefined' || typeof bootstrap === 'undefined') {
        return;
    }

    const PUSAT_MARANG = [5.2072, 103.2054];

    const modal = document.createElement('div');
    modal.className = 'modal fade modal-pilih-lokasi';
    modal.id = 'modalPilihLokasi';
    modal.tabIndex = -1;
    modal.setAttribute('aria-labelledby', 'modalPilihLokasiTajuk');
    modal.setAttribute('aria-hidden', 'true');
    modal.innerHTML = `
        <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-md-down">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <small class="d-none d-md-block fw-semibold text-secondary">KOORDINAT ASET</small>
                        <h5 class="modal-title fw-bold mb-0" id="modalPilihLokasiTajuk">Pilih Lokasi Aset</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-0 position-relative">
                    <div class="peta-pilih-lokasi" data-peta-lokasi></div>

                    <div class="kawalan-peta-lokasi kiri">
                        <button type="button" class="btn btn-light shadow-sm butang-lokasi-saya" data-lokasi-saya aria-label="Guna lokasi saya" title="Guna lokasi saya">
                            <i class="bi bi-crosshair"></i><span class="teks-lokasi-saya ms-1">Guna Lokasi Saya</span>
                        </button>
                    </div>

                    <div class="kawalan-peta-lokasi kanan">
                        <div class="segmen-peta-lokasi" role="group" aria-label="Jenis peta">
                            <button type="button" class="aktif" data-jenis-peta="jalan">Jalan</button>
                            <button type="button" data-jenis-peta="satelit">Satelit</button>
                        </div>
                    </div>

                    <div class="petunjuk-peta-lokasi" data-petunjuk-lokasi>
                        <i class="bi bi-hand-index me-1"></i>Klik pada peta untuk letak pin, atau seret pin untuk ubah kedudukan.
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="me-auto min-w-0">
                        <span class="koordinat-lokasi" data-koordinat-lokasi>Belum dipilih</span>
                        <small class="d-block text-secondary" data-ketepatan-lokasi></small>
                    </div>
                    <button type="button" class="btn btn-light d-none d-md-inline-block" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-utama butang-guna-lokasi" data-guna-lokasi disabled>
                        <i class="bi bi-check2 me-1"></i>Guna Lokasi Ini
                    </button>
                </div>
            </div>
        </div>`;

    document.body.appendChild(modal);

    const modalBs = bootstrap.Modal.getOrCreateInstance(modal);
    const elPeta = modal.querySelector('[data-peta-lokasi]');
    const elKoordinat = modal.querySelector('[data-koordinat-lokasi]');
    const elKetepatan = modal.querySelector('[data-ketepatan-lokasi]');
    const elPetunjuk = modal.querySelector('[data-petunjuk-lokasi]');
    const butangGuna = modal.querySelector('[data-guna-lokasi]');
    const butangLokasiSaya = modal.querySelector('[data-lokasi-saya]');

    let peta = null;
    let pin = null;
    let bulatan = null;
    let medanLat = null;
    let medanLng = null;
    let medanGmaps = null;
    let titik = null;

    const ikonPin = L.divIcon({
        className: 'pin-pilih-lokasi',
        html: '<i class="bi bi-geo-alt-fill"></i>',
        iconSize: [36, 36],
        iconAnchor: [18, 34],
    });

    const lapisan = {
        jalan: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }),
        satelit: L.layerGroup([
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                maxNativeZoom: 18,
                attribution: 'Imej &copy; Esri',
            }),
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                maxNativeZoom: 18,
            }),
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                maxNativeZoom: 18,
            }),
        ]),
    };

    let jenisSemasa = 'jalan';

    const tukarJenis = (jenis) => {
        if (! peta || jenis === jenisSemasa) {
            return;
        }

        peta.removeLayer(lapisan[jenisSemasa]);
        lapisan[jenis].addTo(peta);
        jenisSemasa = jenis;

        modal.querySelectorAll('[data-jenis-peta]').forEach((butang) => {
            butang.classList.toggle('aktif', butang.dataset.jenisPeta === jenis);
        });
    };

    modal.querySelectorAll('[data-jenis-peta]').forEach((butang) => {
        butang.addEventListener('click', () => tukarJenis(butang.dataset.jenisPeta));
    });

    const paparKoordinat = () => {
        if (! titik) {
            elKoordinat.textContent = 'Belum dipilih';
            butangGuna.disabled = true;

            return;
        }

        elKoordinat.textContent = `${titik.lat.toFixed(6)}, ${titik.lng.toFixed(6)}`;
        butangGuna.disabled = false;
    };

    const letakPin = (latlng, { zum = null, ketepatan = null } = {}) => {
        titik = L.latLng(latlng);

        if (! pin) {
            pin = L.marker(titik, { icon: ikonPin, draggable: true, autoPan: true }).addTo(peta);
            pin.on('dragend', () => {
                titik = pin.getLatLng();
                elKetepatan.textContent = '';
                bulatan?.remove();
                bulatan = null;
                paparKoordinat();
            });
        } else {
            pin.setLatLng(titik);
        }

        bulatan?.remove();
        bulatan = null;

        if (ketepatan) {
            bulatan = L.circle(titik, {
                radius: ketepatan,
                color: '#0d625a',
                weight: 1,
                fillOpacity: 0.12,
            }).addTo(peta);
            elKetepatan.textContent = `Ketepatan lokasi ±${Math.round(ketepatan)} m`;
        } else {
            elKetepatan.textContent = '';
        }

        if (zum) {
            peta.setView(titik, zum);
        }

        elPetunjuk.classList.add('tersembunyi');
        paparKoordinat();
    };

    const bacaMedan = () => {
        const lat = parseFloat(medanLat?.value);
        const lng = parseFloat(medanLng?.value);

        if (Number.isFinite(lat) && Number.isFinite(lng) && Math.abs(lat) <= 90 && Math.abs(lng) <= 180 && ! (lat === 0 && lng === 0)) {
            return L.latLng(lat, lng);
        }

        return null;
    };

    const binaPeta = () => {
        peta = L.map(elPeta, { zoomControl: true }).setView(PUSAT_MARANG, 13);
        lapisan.jalan.addTo(peta);
        peta.on('click', (event) => letakPin(event.latlng));
    };

    butangPilih.forEach((butang) => {
        butang.addEventListener('click', () => {
            medanLat = document.querySelector(butang.dataset.lat);
            medanLng = document.querySelector(butang.dataset.lng);
            medanGmaps = butang.dataset.gmaps ? document.querySelector(butang.dataset.gmaps) : null;
            modalBs.show();
        });
    });

    modal.addEventListener('shown.bs.modal', () => {
        if (! peta) {
            binaPeta();
        }

        peta.invalidateSize();

        const sedia = bacaMedan();

        if (sedia) {
            letakPin(sedia, { zum: 17 });
        } else {
            titik = null;
            pin?.remove();
            pin = null;
            bulatan?.remove();
            bulatan = null;
            elKetepatan.textContent = '';
            elPetunjuk.classList.remove('tersembunyi');
            peta.setView(PUSAT_MARANG, 13);
            paparKoordinat();
        }
    });

    /* Lokasi semasa pengguna */
    butangLokasiSaya.addEventListener('click', () => {
        if (! window.isSecureContext || ! navigator.geolocation) {
            elKetepatan.textContent = '';
            window.Swal?.fire({
                icon: 'info',
                title: 'Lokasi auto tidak tersedia',
                text: 'Pelayar hanya membenarkan akses lokasi melalui sambungan HTTPS (atau localhost). Sila klik pada peta untuk letak pin secara manual.',
                confirmButtonText: 'OK',
            });

            return;
        }

        const kandunganAsal = butangLokasiSaya.innerHTML;
        butangLokasiSaya.disabled = true;
        butangLokasiSaya.innerHTML = '<span class="spinner-border spinner-border-sm"></span><span class="teks-lokasi-saya ms-1">Mencari lokasi...</span>';

        navigator.geolocation.getCurrentPosition(
            (kedudukan) => {
                butangLokasiSaya.disabled = false;
                butangLokasiSaya.innerHTML = kandunganAsal;
                letakPin([kedudukan.coords.latitude, kedudukan.coords.longitude], {
                    zum: 18,
                    ketepatan: kedudukan.coords.accuracy,
                });
            },
            (ralat) => {
                butangLokasiSaya.disabled = false;
                butangLokasiSaya.innerHTML = kandunganAsal;

                const mesej = {
                    1: 'Akses lokasi ditolak. Benarkan akses lokasi untuk laman ini dalam tetapan pelayar.',
                    2: 'Lokasi tidak dapat dikesan. Pastikan GPS / perkhidmatan lokasi dihidupkan.',
                    3: 'Masa mencari lokasi tamat. Sila cuba lagi.',
                }[ralat.code] || 'Lokasi tidak dapat diperoleh.';

                window.Swal?.fire({ icon: 'warning', title: 'Lokasi tidak diperoleh', text: mesej, confirmButtonText: 'OK' });
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    });

    /* Pautan Google Maps yang dijana sistem (boleh ditimpa tanpa bertanya) */
    const pautanDijanaSistem = (url) => /^https:\/\/www\.google\.com\/maps\?q=-?[\d.]+,-?[\d.]+$/.test(url.trim());

    /* Guna lokasi → isi medan latitud, longitud dan pautan Google Maps */
    butangGuna.addEventListener('click', async () => {
        if (! titik || ! medanLat || ! medanLng) {
            return;
        }

        const lat = titik.lat.toFixed(6);
        const lng = titik.lng.toFixed(6);
        const medanDiisi = [medanLat, medanLng];

        medanLat.value = lat;
        medanLng.value = lng;

        if (medanGmaps) {
            const pautanBaru = `https://www.google.com/maps?q=${lat},${lng}`;
            const sedia = medanGmaps.value.trim();
            let tulis = sedia === '' || pautanDijanaSistem(sedia);

            if (! tulis && sedia !== pautanBaru) {
                modalBs.hide();

                const jawapan = await window.Swal?.fire({
                    icon: 'question',
                    title: 'Gantikan pautan Google Maps?',
                    text: 'Medan pautan Google Maps sudah ada nilai. Gantikan dengan pautan berdasarkan pin yang dipilih?',
                    showCancelButton: true,
                    confirmButtonText: 'Gantikan',
                    cancelButtonText: 'Kekalkan',
                    reverseButtons: true,
                });

                tulis = Boolean(jawapan?.isConfirmed);
            }

            if (tulis) {
                medanGmaps.value = pautanBaru;
                medanDiisi.push(medanGmaps);
            }
        }

        medanDiisi.forEach((medan) => {
            medan.classList.remove('is-invalid');
            medan.dispatchEvent(new Event('input', { bubbles: true }));
            medan.dispatchEvent(new Event('change', { bubbles: true }));
        });

        modalBs.hide();
    });
});

/* Muat turun (PDF / ZIP): beri maklum balas kerana fail mengambil masa untuk dijana */
document.addEventListener('click', (event) => {
    const pautan = event.target.closest('a[data-muat-turun-berat]');

    if (! pautan) {
        return;
    }

    window.Swal?.fire({
        toast: true,
        position: 'top-end',
        icon: 'info',
        title: 'Menyediakan fail…',
        text: 'Muat turun akan bermula secara automatik.',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
    });
});

/* Bottom sheet muat turun (telefon): [data-lembaran-muat-turun data-pdf data-zip] */
document.addEventListener('DOMContentLoaded', () => {
    if (typeof bootstrap === 'undefined') {
        return;
    }

    const lembaran = document.createElement('div');
    lembaran.className = 'offcanvas offcanvas-bottom panel-penapis-mobile lembaran-muat-turun';
    lembaran.id = 'lembaranMuatTurun';
    lembaran.tabIndex = -1;
    lembaran.setAttribute('aria-labelledby', 'lembaranMuatTurunTajuk');
    lembaran.innerHTML = `
        <div class="pemegang-panel" aria-hidden="true"></div>

        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="lembaranMuatTurunTajuk">Muat Turun</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>

        <div class="offcanvas-body pt-0">
            <div class="kad-senarai-apl">
                <a href="#" class="baris-laporan-apl" data-pautan-pdf data-muat-turun-berat>
                    <span class="ikon-eksport-apl pdf"><i class="bi bi-file-earmark-pdf"></i></span>
                    <span class="teks-baris-laporan">
                        <strong>Maklumat aset (PDF)</strong>
                        <small>Maklumat aset beserta gambar aset</small>
                    </span>
                    <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
                </a>

                <a href="#" class="baris-laporan-apl" data-pautan-zip data-muat-turun-berat>
                    <span class="ikon-eksport-apl zip"><i class="bi bi-file-earmark-zip"></i></span>
                    <span class="teks-baris-laporan">
                        <strong>Maklumat + dokumen asal (ZIP)</strong>
                        <small>PDF maklumat dan semua dokumen yang dimuat naik</small>
                    </span>
                    <i class="bi bi-chevron-right anak-panah-apl" aria-hidden="true"></i>
                </a>
            </div>
        </div>`;

    document.body.appendChild(lembaran);

    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(lembaran);
    const pautanPdf = lembaran.querySelector('[data-pautan-pdf]');
    const pautanZip = lembaran.querySelector('[data-pautan-zip]');

    // Delegasi: butang mungkin berada dalam kandungan modal yang dimuat kemudian
    document.addEventListener('click', (event) => {
        const butang = event.target.closest('[data-lembaran-muat-turun]');

        if (! butang) {
            return;
        }

        event.preventDefault();
        pautanPdf.href = butang.dataset.pdf;
        pautanZip.href = butang.dataset.zip;
        offcanvas.show();
    });

    [pautanPdf, pautanZip].forEach((pautan) => {
        pautan.addEventListener('click', () => window.setTimeout(() => offcanvas.hide(), 150));
    });
});

