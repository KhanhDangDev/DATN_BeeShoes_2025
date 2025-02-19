<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
//////// admin.

// Sản phẩm
Route::get('/khach-hang', [CustomerController::class, 'index']);
Route::get('/khach-hang/{id}', [CustomerController::class, 'showCustomer']);
Route::post('/add-khach-hang', [CustomerController::class, 'storeCustomer']);
Route::put('/update-khach-hang', [CustomerController::class, 'updateCustomer']);