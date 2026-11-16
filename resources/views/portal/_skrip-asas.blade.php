<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => navigator.serviceWorker.register(@json(route('portal.sw', [], false)), { scope: '/awam' }).catch(() => {}));
    }

    // Laci menu: tutup dahulu, baru pergi ke halaman lain. Bila kembali (Back), pastikan laci tertutup.
    (() => {
        const laci = document.getElementById('laciPortal');
        if (!laci) return;

        const tetapSemula = () => {
            laci.classList.remove('show', 'showing', 'hiding');
            laci.removeAttribute('aria-modal');
            laci.removeAttribute('role');
            document.querySelectorAll('.offcanvas-backdrop').forEach((b) => b.remove());
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        };

        laci.querySelectorAll('a[href]').forEach((pautan) => pautan.addEventListener('click', (e) => {
            if (pautan.getAttribute('href').startsWith('tel:') || pautan.getAttribute('href').startsWith('mailto:')) return;
            e.preventDefault();
            tetapSemula();
            location.href = pautan.href;
        }));

        window.addEventListener('pageshow', tetapSemula);
    })();

    document.querySelectorAll('[data-kongsi]').forEach((b) => b.addEventListener('click', async () => {
        const data = { title: document.title, url: location.href };
        if (navigator.share) {
            try { await navigator.share(data); } catch (e) {}
            return;
        }
        try {
            await navigator.clipboard.writeText(location.href);
            Swal.fire({ toast: true, position: 'top', icon: 'success', title: 'Pautan disalin', showConfirmButton: false, timer: 1600 });
        } catch (e) {}
    }));

    document.querySelectorAll('img[data-muat]').forEach((img) => {
        const siap = () => img.closest('.portal-rangka')?.classList.add('dimuat');
        img.complete ? siap() : img.addEventListener('load', siap);
        img.addEventListener('error', siap);
    });
</script>
