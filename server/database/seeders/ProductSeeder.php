<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Testing\Fakes\Fake;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use App\Constants\ProductStatus;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Material;
use App\Models\Size;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $id_chat_lieu = Material::pluck('id')->toArray();

        $id_mau_sac = Color::pluck('id')->toArray(); // Lấy tất cả id của màu sắc.

        $id_thuong_hieu = Brand::pluck('id')->toArray();

        $ngay_tao_1 = Carbon::now()->setTimezone('Asia/Ho_Chi_Minh'); // lấy thời gian theo hiện tại.
        $ngay_tao_2 = Carbon::parse($ngay_tao_1)->addMinutes(1);
        $ngay_tao_3 = Carbon::parse($ngay_tao_2)->addMinutes(1);
        $ngay_tao_4 = Carbon::parse($ngay_tao_3)->addMinutes(1);

        DB::table('san_pham')->insert([
            'id' => $faker->uuid(),
            'ma_san_pham' => 'SKU001',
            'ten_san_pham' => 'Giày thể thao',
            'mo_ta' => 'vừa vặn',
            'don_gia' => 100000,
            'trang_thai' => ProductStatus::DANG_KINH_DOANH,
            'id_mau_sac' => $faker->randomElement($id_mau_sac), // Chọn ngẫu nhiên một id_mau_sac hợp lệ.
            'id_chat_lieu' => $faker->randomElement($id_chat_lieu),
            'id_thuong_hieu' => $faker->randomElement($id_thuong_hieu),
            'ngay_tao' => $ngay_tao_1
        ]);

        DB::table('san_pham')->insert([
            'id' => $faker->uuid(),
            'ma_san_pham' => 'SKU002',
            'ten_san_pham' => 'Giày sneaker',
            'mo_ta' => 'hơi chật',
            'don_gia' => 300000,
            'trang_thai' => ProductStatus::DANG_KINH_DOANH,
            'id_mau_sac' => $faker->randomElement($id_mau_sac), // Chọn ngẫu nhiên một id_mau_sac hợp lệ.
            'id_chat_lieu' => $faker->randomElement($id_chat_lieu),
            'id_thuong_hieu' => $faker->randomElement($id_thuong_hieu),
            'ngay_tao' => $ngay_tao_2
        ]);

        DB::table('san_pham')->insert([
            'id' => $faker->uuid(),
            'ma_san_pham' => 'SKU003',
            'ten_san_pham' => 'Giày lười',
            'mo_ta' => 'thoải mái',
            'don_gia' => 300000,
            'trang_thai' => ProductStatus::DANG_KINH_DOANH,
            'id_mau_sac' => $faker->randomElement($id_mau_sac), // Chọn ngẫu nhiên một id_mau_sac hợp lệ.
            'id_chat_lieu' => $faker->randomElement($id_chat_lieu),
            'id_thuong_hieu' => $faker->randomElement($id_thuong_hieu),
            'ngay_tao' => $ngay_tao_3
        ]);

        DB::table('san_pham')->insert([
            'id' => $faker->uuid(),
            'ma_san_pham' => 'SKU004',
            'ten_san_pham' => 'Giày Jordan',
            'mo_ta' => 'đẹp',
            'don_gia' => 400000,
            'trang_thai' => ProductStatus::DANG_KINH_DOANH,
            'id_mau_sac' => $faker->randomElement($id_mau_sac), // Chọn ngẫu nhiên một id_mau_sac hợp lệ.
            'id_chat_lieu' => $faker->randomElement($id_chat_lieu),
            'id_thuong_hieu' => $faker->randomElement($id_thuong_hieu),
            'ngay_tao' => $ngay_tao_4
        ]);
    }
}
