<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::middleware(['api'])->group(function () {
    require __DIR__ . '/api/san-pham/api.php';
    require __DIR__ . '/api/thuoc-tinh/api.php';
});
