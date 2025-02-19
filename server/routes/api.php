<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    require __DIR__ . '/api/san-pham/api.php';
    require __DIR__ . '/api/thuoc-tinh/api.php';
    require __DIR__ . '/api/khach-hang/api.php';
    require __DIR__ . '/api/nhan-vien/api.php';
});
