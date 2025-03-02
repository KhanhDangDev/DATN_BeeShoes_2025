<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
//////// admin.

// Sản phẩm
Route::get('/danh-sach-san-pham', [ProductController::class, 'index']);
Route::get('/danh-sach-attribute', [ProductController::class, 'indexAttribute']);
Route::post('/them-san-pham', [ProductController::class, 'store']);
Route::post('/them-kich-co', [ProductController::class, 'storeKichCo']);
Route::get('/tim-san-pham/{id}', [ProductController::class, 'show']);
Route::put('/update-san-pham', [ProductController::class, 'update']);
Route::put('/update-trang-thai-san-pham', [ProductController::class, 'updateTrangThaiSanPham']);
Route::put('/update-so-luong-kich-co', [ProductController::class, 'updateSoluongKichCo']);
