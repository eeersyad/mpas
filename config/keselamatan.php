<?php

return [

    // Hidupkan / matikan Content-Security-Policy. Matikan sementara jika ada halaman rosak:
    // KESELAMATAN_CSP=false dalam .env, kemudian "php artisan optimize:clear".
    'csp' => env('KESELAMATAN_CSP', true),

];
