<?php

use App\Http\Controllers\Api\AttributesController;
use Illuminate\Support\Facades\Route;

Route::get('/danh-sach-thuong-hieu', [AttributesController::class, 'indexBrand']);
