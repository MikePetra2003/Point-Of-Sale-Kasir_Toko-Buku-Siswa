<?php

return [
    'allow_owner' => env('KASIR_ALLOW_OWNER', true),
    'tampilkan_ppn' => env('KASIR_TAMPILKAN_PPN', false),
    'persen_ppn' => (float) env('KASIR_PERSEN_PPN', 11),
    'lebar_struk_mm' => env('KASIR_LEBAR_STRUK_MM', 80),
    'health_probe_interval_ms' => 10000,
    'sync_retry_delay_ms' => 2000,
];
