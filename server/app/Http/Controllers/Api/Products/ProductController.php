<?php

namespace App\Http\Controllers\Api\Products;

use App\Constants\ProductStatus;
use App\Exceptions\NotFoundException;
use App\Exceptions\RestApiException;
use App\Helpers\ApiResponse;
use App\Helpers\ConvertHelper;
use App\Helpers\CustomCodeHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\AttributeRequestBody;
use App\Http\Requests\Product\ProductRequest;
use App\Http\Requests\Product\ProductRequestBody;
use App\Http\Resources\Products\AttributeResource;
use App\Http\Resources\Products\ImageResource;
use App\Http\Resources\Products\ProductDetailResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductDetails;
use App\Models\Size;
use Cloudinary\Api\ApiClient;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;

class ProductController extends Controller
{

    public function index(ProductRequest $req)
    {
        $products = Product::getProducts($req);

        $statusCounts = Product::select(DB::raw('count(status) as count, status'))
            ->groupBy('status')
            ->get();
        $brands = Brand::select(['id', 'name'])->get();
        $categories = Category::select(['id', 'name'])->get();

        $otherData['brands'] = $brands;
        $otherData['categories'] = $categories;

        return ApiResponse::responsePageCustom($products, $statusCounts, $otherData);
    }

   
    public function indexAttributes()
    {
        $brands = Brand::select(['id', 'name'])->where('status', '=', ProductStatus::IS_ACTIVE)->orderBy('created_at', 'desc')->get();
        $categories = Category::select(['id', 'name'])->where('status', '=', ProductStatus::IS_ACTIVE)->orderBy('created_at', 'desc')->get();
        $colors = Color::select(['id', 'code', 'name'])->where('status', '=', ProductStatus::IS_ACTIVE)->orderBy('created_at', 'desc')->get();
        $sizes = Size::select(['id', 'name'])->where('status', '=', ProductStatus::IS_ACTIVE)->orderBy('created_at', 'desc')->get();

        $data['brands'] = $brands;
        $data['categories'] = $categories;
        $data['colors'] = $colors;
        $data['sizes'] = $sizes;

        return ApiResponse::responseObject($data);
    }

    public function store(ProductRequestBody $req)
    {

        $data = json_decode($req->data);
        $files = $req->file('files');

        DB::beginTransaction();

        try {
            // convert req
            $newProduct = new Product();
            $newProduct->code = $data->code;
            $newProduct->name = $data->name;
            $newProduct->status = $data->status;
            $newProduct->description = $data->description;
            $newProduct->brand_id = $data->brandId;
            $newProduct->save();

            foreach ($data->categoryIds as $categoryId) {
                $newProductCategory = new ProductCategory();
                $newProductCategory->category_id = $categoryId;
                $newProductCategory->product_id = $newProduct->id;
                $newProductCategory->save();
            }

            foreach ($data->productItems as $productItem) {
                $newProductItem = new ProductDetails();
                $newProductItem->sku = $productItem->sku;
                $newProductItem->color_id = $productItem->colorId;
                $newProductItem->size_id = $productItem->sizeId;
                $newProductItem->price = $productItem->price;
                $newProductItem->quantity = $productItem->quantity;
                $newProductItem->status = $productItem->status;
                $newProductItem->product_id = $newProduct->id;
                $newProductItem->save();
            }

            $images = $data->images;

            $client = new Client();
            $cloudName = 'dgupbx2im';
            $uploadPreset = 'ml_default';

            foreach ($files as $file) {
                $promises[] = $client->postAsync("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                    'multipart' => [
                        [
                            'name' => 'file',
                            'contents' => fopen($file->getRealPath(), 'r'),
                        ],
                        [
                            'name' => 'upload_preset',
                            'contents' => $uploadPreset,
                        ],
                        [
                            'name' => 'folder',
                            'contents' => 'products',
                        ],
                    ],
                ]);
            };

            $results = Utils::unwrap($promises);
            foreach ($results as $index => $result) {
                $response = json_decode($result->getBody(), true);
                $url = $response['secure_url'];
                $image = $images[$index];
                $publicId = $response['public_id'];


                $newImage = new Image();
                $newImage->path_url = $url;
                $newImage->is_default = $image->isDefault;
                $newImage->public_id = $publicId;
                $newImage->product_id = $newProduct->id;
                $newImage->save();
            }
            DB::commit();
        } catch (\Exception  $e) {
            DB::rollback();
            throw new RestApiException($e->getMessage());
        }

        $response = $newProduct;

        return ApiResponse::responseObject($response);
    }

    public function show($id)
    {
        $response = $this->findById($id);
        return ApiResponse::responseObject($response);
    }

    public function updateStatus(ProductRequestBody $req)
    {
        $product = Product::find($req->id);

        if (!$product) {
            throw new NotFoundException("Không tìm thấy sản phẩm có id là " . $req->id);
        }

        $product->status = $req->statusProduct;
        $product->update();

        $products = Product::getProducts($req);

        $statusCounts = Product::select(DB::raw('count(status) as count, status'))
            ->groupBy('status')
            ->get();

        $response['products'] = $products['data'];
        $response['statusCounts'] = $statusCounts;
        $response['totalPages'] = $products['totalPages'];

        return ApiResponse::responseObject($response);
    }

    public function update(ProductRequestBody $req)
    {

        $data = json_decode($req->data);
        $files = $req->file('files');

        $product = Product::find($data->id);

        if (!$product) {
            throw new NotFoundException("Không tìm thấy sản phẩm có id là " . $data->id);
        }

        if ($data->name !== $product->name) {
            $existingProduct = Product::where('name', '=', $data->name)->first();

            if ($existingProduct) {
                throw new RestApiException("Tên sản phẩm này tồn tại!");
            }
        }

        if ($data->code !== $product->code) {
            $existingProduct = Product::where('code', '=', $data->code)->first();

            if ($existingProduct) {
                throw new RestApiException("Mã sản phẩm này tồn tại!");
            }
        }

        DB::beginTransaction();

        try {
            $product->code = $data->code;
            $product->name = $data->name;
            $product->status = $data->status;
            $product->description = $data->description;
            $product->brand_id = $data->brandId;
            $product->update();

            ProductCategory::where('product_id', $data->id)->delete();

            foreach ($data->categoryIds as $categoryId) {
                $newProductCategory = new ProductCategory();
                $newProductCategory->category_id = $categoryId;
                $newProductCategory->product_id = $product->id;
                $newProductCategory->save();
            }

            if (count($data->productItemsNeedRemove) > 0) {
                ProductDetails::whereIn('id', $data->productItemsNeedRemove)->delete();
            }

            foreach ($data->productItems as $productItem) {
                if (property_exists($productItem, 'id')) {
                    $findProductItem = ProductDetails::find($productItem->id);
                    $findProductItem->sku = $productItem->sku;
                    $findProductItem->price = $productItem->price;
                    $findProductItem->quantity = $productItem->quantity;
                    $findProductItem->status = $productItem->status;
                    $findProductItem->update();
                } else {
                    $newProductItem = new ProductDetails();
                    $newProductItem->sku = $productItem->sku;
                    $newProductItem->color_id = $productItem->colorId;
                    $newProductItem->size_id = $productItem->sizeId;
                    $newProductItem->price = $productItem->price;
                    $newProductItem->quantity = $productItem->quantity;
                    $newProductItem->status = $productItem->status;
                    $newProductItem->product_id = $data->id;
                    $newProductItem->save();
                }
            }

            if (count($data->imagesNeedRemove) > 0) {
                Image::whereIn('id', $data->imagesNeedRemove)->delete();

                foreach ($data->imagesCloudNeedRemove as $publicId) {
                    Cloudinary::destroy($publicId);
                }
            }

            foreach ($data->images as $image) {
                if (property_exists($image, 'id')) {
                    $findImage = Image::find($image->id);
                    $findImage->is_default = $image->isDefault;
                    $findImage->update();
                }
            }

            if (isset($files) && count($files) > 0) {
                $client = new Client();
                $cloudName = 'dgupbx2im';
                $uploadPreset = 'ml_default';
                foreach ($files as $file) {

                    $promises[] = $client->postAsync("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                        'multipart' => [
                            [
                                'name' => 'file',
                                'contents' => fopen($file->getRealPath(), 'r'),
                            ],
                            [
                                'name' => 'upload_preset',
                                'contents' => $uploadPreset,
                            ],
                            [
                                'name' => 'folder',
                                'contents' => 'products',
                            ],
                        ],
                    ]);
                };

                $results = Utils::unwrap($promises);
                foreach ($results as $index => $result) {
                    $response = json_decode($result->getBody(), true);
                    $url = $response['secure_url'];

                    $image = $data->imagesNeedCreate[$index];

                    $newImage = new Image();
                    $newImage->path_url = $url;
                    $newImage->is_default = $image->isDefault;
                    $newImage->public_id = $response['public_id'];
                    $newImage->product_color_id = $image->colorId;
                    $newImage->product_id = $data->id;
                    $newImage->save();
                }
            }

            DB::commit();
        } catch (\Exception  $e) {
            DB::rollback();
            throw new RestApiException($e->getMessage());
        }

        $response = $this->findById($data->id);

        return ApiResponse::responseObject($response);
    }

    private function findById($id)
    {

        $product = Product::find($id);

        if (!$product) {
            throw new NotFoundException("Không tìm thấy sản phẩm có id là " . $id);
        }

        // categories
        $productCategoryIds = ProductCategory::where('product_id', $id)->pluck('category_id');
        $categories = Category::whereIn('id', $productCategoryIds)->get();

        // product_details
        $productItems = ProductDetails::where('product_id', '=', $id)->get();

        // images
        $images = Image::where('product_id', $id)->get();

        $variants = $productItems->map(function ($productItem) {
                return [
                    'variantOrder' => $productItem->id,
                    'sku' => $productItem->sku,
                    'colorId' => Color::find($productItem->color_id),
                    'sizeId' =>  Size::find($productItem->size_id),
                    'price' => ConvertHelper::formatCurrencyVnd($productItem->price),
                    'quantity' => ConvertHelper::formatNumberString($productItem->quantity),
                    'status' => $productItem->status,
                ];
            })->toArray();

        $response = new ProductDetailResource($product);
        $response['categories'] = AttributeResource::collection($categories);
        $response['variants'] = $variants;
        $response['images'] = ImageResource::collection($images);

        return $response;
    }

    public function indexHomeClient()
    {
        $productNews = ProductDetails::getClientProductHome();
        $productTopSold = ProductDetails::getClientProductTopSold();

        $response['new'] = $productNews;
        $response['topSold'] = $productTopSold;

        return ApiResponse::responseObject($response);
    }

    public function clientIndexMale(ProductRequest $req)
    {
        $req->pageSize = 15;
        $productDetails = ProductDetails::getClientProducts($req, 'male');

        $brands = Brand::select(['id', 'name'])->orderBy('created_at', 'desc')->get();
        $categories = Category::select(['id', 'name'])->orderBy('created_at', 'desc')->get();
        $colors = Color::select(['id', 'code', 'name'])->orderBy('created_at', 'desc')->get();
        $sizes = Size::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $otherData['brands'] = $brands;
        $otherData['categories'] = $categories;
        $otherData['colors'] = $colors;
        $otherData['sizes'] = $sizes;

        return ApiResponse::responsePageCustom($productDetails, [], $otherData);
    }
    public function clientIndexFemale(ProductRequest $req)
    {
        $req->pageSize = 15;
        $productDetails = ProductDetails::getClientProducts($req, 'female');

        $brands = Brand::select(['id', 'name'])->orderBy('created_at', 'desc')->get();
        $categories = Category::select(['id', 'name'])->orderBy('created_at', 'desc')->get();
        $colors = Color::select(['id', 'code', 'name'])->orderBy('created_at', 'desc')->get();
        $sizes = Size::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $otherData['brands'] = $brands;
        $otherData['categories'] = $categories;
        $otherData['colors'] = $colors;
        $otherData['sizes'] = $sizes;

        return ApiResponse::responsePageCustom($productDetails, [], $otherData);
    }

    public function clientIndex(ProductRequest $req)
    {
        $req->pageSize = 15;
        $productDetails = ProductDetails::getClientProducts($req);

        $brands = Brand::select(['id', 'name'])->orderBy('created_at', 'desc')->get();
        $categories = Category::select(['id', 'name'])->orderBy('created_at', 'desc')->get();
        $colors = Color::select(['id', 'code', 'name'])->orderBy('created_at', 'desc')->get();
        $sizes = Size::select(['id', 'name'])->orderBy('name', 'asc')->get();

        $otherData['brands'] = $brands;
        $otherData['categories'] = $categories;
        $otherData['colors'] = $colors;
        $otherData['sizes'] = $sizes;

        return ApiResponse::responsePageCustom($productDetails, [], $otherData);
    }

    public function findBySkuClient($sku)
    {
        $product = Product::select('PRODUCTS.name', 'BRANDS.NAME as brandName', 'COLORS.NAME as colorName', 'PRODUCTS.status', 'PRODUCT_DETAILS.price', 'PRODUCT_DETAILS.sku', 'PRODUCTS.ID as productId', 'COLORS.ID as colorId', 'PRODUCTS.description')
            ->join('PRODUCT_DETAILS', 'PRODUCTS.ID', '=', 'PRODUCT_DETAILS.PRODUCT_ID')
            ->join('BRANDS', 'PRODUCTS.BRAND_ID', '=', 'BRANDS.ID')
            ->join('COLORS', 'PRODUCT_DETAILS.COLOR_ID', '=', 'COLORS.ID')
            ->where('PRODUCT_DETAILS.SKU', $sku)
            ->groupBy('PRODUCTS.NAME', 'BRANDS.NAME', 'COLORS.NAME', 'PRODUCTS.STATUS', 'PRODUCT_DETAILS.PRICE', 'PRODUCT_DETAILS.SKU', 'PRODUCTS.ID', 'COLORS.ID')
            ->first();

        if (!$product) {
            throw new NotFoundException("Không tìm thấy sản phẩm này!");
        }

        if ($product->status !== ProductStatus::IS_ACTIVE) {
            throw new NotFoundException("Không tìm thấy sản phẩm này!");
        }

        // colors
        $colors = ProductDetails::select('product_details.color_id as colorId', 'product_details.sku', 'colors.name', 'colors.code')
            ->where('product_details.product_id', $product->productId)
            ->join('colors', 'product_details.color_id', '=', 'colors.id')
            ->groupBy('color_id', 'sku')
            ->get();

        // sizes
        $productActiveStatus = 'is_active';
        $sizes = ProductDetails::select('product_details.quantity', 'product_details.id', 'sizes.name', 'product_details.status')
            ->where('product_details.product_id', $product->productId)
            ->where('product_details.sku', $sku)
            ->where('product_details.status', $productActiveStatus)
            ->join('sizes', 'product_details.size_id', '=', 'sizes.id')
            ->orderBy('sizes.name', 'asc')
            ->get();

        // images
        $images = Image::select('path_url as pathUrl', 'is_default as isDefault')->where('product_id', $product->productId)->get();

        $product['colors'] = $colors;
        $product['sizes'] = $sizes;
        $product['images'] = $images;

        return ApiResponse::responseObject($product);
    }

    public function findByClientId($id)
    {
        $productDetail = ProductDetails::getClientProductDetailById($id)->first();

        if (!$productDetail) {
            throw new NotFoundException("Không tìm thấy sản phẩm này!");
        }

        // if ($productDetail->stock <= 0) {
        //     throw new RestApiException("Sản phẩm tạm hết hàng");
        // }

        $productDetail['quantity'] = 1;
        $productActiveStatus = 'is_active';

        $sizes = ProductDetails::select('product_details.quantity', 'product_details.id', 'sizes.name', 'product_details.status')
            ->where('product_details.sku', $productDetail->sku)
            ->where('product_details.status', $productActiveStatus)
            ->join('sizes', 'product_details.size_id', '=', 'sizes.id')
            ->orderBy('sizes.name', 'asc')
            ->get();
        $productDetail['sizes'] = $sizes;


        return ApiResponse::responseObject($productDetail);
    }

    public function top8ProductNew(ProductRequest $req)
    {
        $req->pageSize = 8;
        $productDetails = ProductDetails::getClientProducts($req);

        return ApiResponse::responsePageCustom($productDetails);
    }

    public function top8ProductHot(ProductRequest $req)
    {
        $req->pageSize = 8;
        $productDetails = ProductDetails::getClientProducts($req);

        return ApiResponse::responsePageCustom($productDetails);
    }
}
