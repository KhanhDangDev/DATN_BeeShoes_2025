<?php

use App\Http\Controllers\Api\AttributesController;
use Illuminate\Support\Facades\Route;

Route::get('/danh-sach-thuong-hieu', [AttributesController::class, 'indexBrand']);
Route::get('/danh-sach-chat-lieu', [AttributesController::class, 'indexMaterial']);
Route::get('/danh-sach-mau-sac', [AttributesController::class, 'indexColor']);

Route::get('/danh-sach-thuong-hieu/{id}', [AttributesController::class, 'showBrand']);
Route::get('/danh-sach-chat-lieu/{id}', [AttributesController::class, 'showMaterial']);
Route::get('/danh-sach-mau-sac/{id}', [AttributesController::class, 'showColor']);

Route::post('/add-thuong-hieu', [AttributesController::class, 'storeBrand']);
Route::put('/update-thuong-hieu', [AttributesController::class, 'updateBrand']);

Route::post('/add-chat-lieu', [AttributesController::class, 'storeMaterial']);
Route::put('/update-chat-lieu', [AttributesController::class, 'updateMaterial']);

Route::post('/add-mau-sac', [AttributesController::class, 'storeColor']);
Route::put('/update-mau-sac', [AttributesController::class, 'updateColor']);

Route::put('/update-trang-thai', [AttributesController::class, 'updateStatus']);
