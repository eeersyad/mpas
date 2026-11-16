<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => navigator.serviceWorker.register(@json(route('portal.sw', [], false)), { scope: '/awam' }).catch(() => {}));
    }

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
