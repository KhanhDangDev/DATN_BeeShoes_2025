<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StaffController;
//////// admin.

// Sản phẩm
Route::get('/nhan-vien', [StaffController::class, 'index']);
Route::get('/nhan-vien/{id}', [StaffController::class, 'showStaff']);
Route::post('/add-nhan-vien', [StaffController::class, 'storeStaff']);
Route::put('/update-nhan-vien', [StaffController::class, 'updateStaff']);