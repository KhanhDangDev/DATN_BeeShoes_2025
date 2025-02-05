<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    require __DIR__ . '/api/san-pham/api.php';
    require __DIR__ . '/api/thuoc-tinh/api.php';
});
