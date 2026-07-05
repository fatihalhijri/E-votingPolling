{{--
    Komponen x-public-layout
    File ini menjadi jembatan antara <x-public-layout> di Blade
    dengan file layouts/public.blade.php yang sebenarnya.

    Kenapa file ini diperlukan?
    Laravel anonymous component (<x-nama>) mencari file di:
    resources/views/components/nama.blade.php ATAU
    resources/views/components/nama/index.blade.php

    Dengan file ini, <x-public-layout> → layouts/public.blade.php
    (menggunakan $slot bawaan anonymous component)
--}}
@include('layouts.public')
