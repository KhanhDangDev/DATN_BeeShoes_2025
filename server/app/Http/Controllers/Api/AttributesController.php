<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttributesController extends Controller
{
    // index
    public function indexBrand(Request $request)
    {
        return ApiResponse::responseObject(
            "Hello"
        );
    }
    public function indexColor(Request $request) {}
    public function indexMaterial(Request $request) {}

    // store
    public function storeBrand(Request $request) {}
    public function storeColor(Request $request) {}
    public function storeMaterial(Request $request) {}

    // update
    public function updateBrand(Request $request) {}
    public function updateColor(Request $request) {}
    public function updateMaterial(Request $request) {}

    // show
    public function showBrand(Request $request) {}
    public function showColor(Request $request) {}
    public function showMaterial(Request $request) {}
}
