<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NotFoundException;
use App\Exceptions\RestApiException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributesController extends Controller
{
    // index
    public function indexBrand(Request $request)
    {
        return ApiResponse::responseObject(
            "Hello"
        );
    }
    public function indexColor(Request $request)
    {
    }
    public function indexMaterial(Request $request)
    {
    }

    public function createModelInstance($tableName)
    {
        $modelClass = match ($tableName) {
            'thuong_hieu' => 'App\Models\Brand',
            default => throw new RestApiException("Model not found for table: $tableName"),
        };

        return new $modelClass();
    }

    public function attributeObject($tableName, $attributeNameVN)
    {
        $messageIsExists = ' đã tồn tại!';
        $attributeObject = [
            'tableName' => $tableName,
            'fieldCode' => 'ma_' . $tableName,
            'fieldName' => 'ten_' . $tableName,
            'codeMessageThrow' => 'Mã ' . $attributeNameVN . $messageIsExists,
            'nameMessageThrow' => 'Tên ' . $attributeNameVN . $messageIsExists,
            'isNotExitsMessageThrow' => 'Không tìm thấy ' . $attributeNameVN . ' này!',
        ];

        return $attributeObject;
    }

    /* storeAttributesCommon (method post)
    *  @params:
    *  $attributeObject: {
    *    tableName,
    *    fieldCode,
    *    fieldName,
    *    codeMessageThrow,
    *    nameMessageThrow
    *  }
    */

    public function storeAttributesCommon($attributeObject, Request $req)
    {
        $tableName = $attributeObject['tableName'];
        $code = $attributeObject['fieldCode'];
        $name = $attributeObject['fieldName'];
        $messageCodeThrow = $attributeObject['codeMessageThrow'];
        $messageNameThrow = $attributeObject['nameMessageThrow'];

        $validatedData = $req->validate([
            'id' => 'required|string',
            'ma' => 'required|string',
            'ten' => 'required|string',
        ], [
            'id.required' => 'Id không được bỏ trống.',
            'ma.required' => 'Mã không được bỏ trống.',
            'ten.required' => 'Tên không được bỏ trống.',
        ]);

        $attributeCodeExists = DB::table($tableName)->where($code, $req->ma)->first();

        if ($attributeCodeExists) {
            throw new RestApiException($messageCodeThrow);
        }

        $attributeNameExists = DB::table($tableName)->where($name, $req->ten)->first();

        if ($attributeNameExists) {
            throw new RestApiException($messageNameThrow);
        }

        $modelAttribute = $this->createModelInstance($tableName);
        $modelAttribute->$code = $req->ma;
        $modelAttribute->$name = $req->ten;
        $modelAttribute->save();

        return $modelAttribute;
    }

    public function storeBrand(Request $req)
    {
        $attributeObj = $this->attributeObject("thuong_hieu", "Thương hiệu");
        $createdModel = $this->storeAttributesCommon($attributeObj, $req);
        return ApiResponse::responseObject($createdModel);
    }
    public function storeColor(Request $req)
    {
        $attributeObj = $this->attributeObject("mau_sac", "Màu sắc");
        $createdModel = $this->storeAttributesCommon($attributeObj, $req);
        return ApiResponse::responseObject($createdModel);
    }
    public function storeMaterial(Request $req)
    {
        $attributeObj = $this->attributeObject("chat_lieu", "Chất liệu");
        $createdModel = $this->storeAttributesCommon($attributeObj, $req);
        return ApiResponse::responseObject($createdModel);
    }

    /* uodateAttributesCommon (method put)
    *  @params:
    *  $attributeObject: {
    *    tableName,
    *    fieldCode,
    *    fieldName,
    *    codeMessageThrow,
    *    nameMessageThrow
    *  }
    */

    public function updateAttributesCommon($attributeObject, Request $req)
    {
        $tableName = $attributeObject['tableName'];
        $code = $attributeObject['fieldCode'];
        $name = $attributeObject['fieldName'];
        $messageCodeThrow = $attributeObject['codeMessageThrow'];
        $messageNameThrow = $attributeObject['nameMessageThrow'];
        $messageIsExitsThrow = $attributeObject['isNotExitsMessageThrow'];

        $validatedData = $req->validate([
            'id' => 'required|string',
            'ma' => 'required|string',
            'ten' => 'required|string',
        ], [
            'id.required' => 'Id không được bỏ trống.',
            'ma.required' => 'Mã không được bỏ trống.',
            'ten.required' => 'Tên không được bỏ trống.',
        ]);

        $findAttribute = $this->createModelInstance($tableName)::find($req->id);

        if (!$findAttribute) {
            throw new NotFoundException($messageIsExitsThrow);
        }

        if ($req->ma !== $findAttribute->$code) {
            $attributeCodeExists = DB::table($tableName)->where($code, $req->ma)->first();

            if ($attributeCodeExists) {
                throw new RestApiException($messageCodeThrow);
            }
        }

        if ($req->ten !== $findAttribute->$name) {
            $attributeNameExists = DB::table($tableName)->where($name, $req->ten)->first();

            if ($attributeNameExists) {
                throw new RestApiException($messageNameThrow);
            }
        }

        $findAttribute->$code = $req->ma;
        $findAttribute->$name = $req->ten;
        $findAttribute->save();

        return $findAttribute;
    }

    public function updateBrand(Request $req)
    {
        $attributeObj = $this->attributeObject("thuong_hieu", "Thương hiệu");
        $updatedModel = $this->updateAttributesCommon($attributeObj, $req);
        return ApiResponse::responseObject($updatedModel);
    }
    public function updateColor(Request $req)
    {
        $attributeObj = $this->attributeObject("mau_sac", "Màu sắc");
        $updatedModel = $this->updateAttributesCommon($attributeObj, $req);
        return ApiResponse::responseObject($updatedModel);
    }
    public function updateMaterial(Request $req)
    {
        $attributeObj = $this->attributeObject("chat_lieu", "Chất liệu");
        $updatedModel = $this->updateAttributesCommon($attributeObj, $req);
        return ApiResponse::responseObject($updatedModel);
    }

    // show
    public function showBrand(Request $request)
    {
    }
    public function showColor(Request $request)
    {
    }
    public function showMaterial(Request $request)
    {
    }

    // destroy
    public function destroyBrand(Request $request)
    {
    }
    public function destroyColor(Request $request)
    {
    }
    public function destroyMaterial(Request $request)
    {
    }
}
