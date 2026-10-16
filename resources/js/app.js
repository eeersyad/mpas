import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    */
    const aplikasi = document.querySelector('#aplikasi');
    const toggleSidebar = document.querySelector('#toggleSidebar');

    if (aplikasi && toggleSidebar) {
        const statusDisimpan = localStorage.getItem('mpas_sidebar');

        if (statusDisimpan === 'runtuh') {
            aplikasi.classList.add('sidebar-runtuh');
            toggleSidebar.setAttribute('aria-expanded', 'false');
            toggleSidebar.setAttribute('aria-label', 'Besarkan sidebar');
        }

        toggleSidebar.addEventListener('click', () => {
            const runtuh = aplikasi.classList.toggle('sidebar-runtuh');

            toggleSidebar.setAttribute('aria-expanded', String(!runtuh));
            toggleSidebar.setAttribute(
                'aria-label',
                runtuh ? 'Besarkan sidebar' : 'Kecilkan sidebar'
            );

            localStorage.setItem('mpas_sidebar', runtuh ? 'runtuh' : 'besar');
        });
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
    | Borang aset bangunan
    |--------------------------------------------------------------------------
    */
    const borangBangunan = document.querySelector('[data-borang-bangunan]');

    if (!borangBangunan) {
        return;
    }

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
            const peratus = ((langkahBangunanSemasa - 1) / 5) * 100;
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
});