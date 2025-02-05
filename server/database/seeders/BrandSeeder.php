<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Constants\CommonStatus;
use Faker\Factory as Faker;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $ngay_tao_1 = Carbon::now()->setTimezone('Asia/Ho_Chi_Minh');
        $ngay_tao_2 = Carbon::parse($ngay_tao_1)->addMinutes(1);
        $ngay_tao_3 = Carbon::parse($ngay_tao_2)->addMinutes(1);

        DB::table('thuong_hieu')->insert([
            'id' => $faker->uuid(),
            'ma_thuong_hieu' => 'TH0001',
            'ten_thuong_hieu' => 'Nike',
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
            'ngay_tao' => $ngay_tao_1
        ]);

        DB::table('thuong_hieu')->insert([
            'id' => $faker->uuid(),
            'ma_thuong_hieu' => 'TH0002',
            'ten_thuong_hieu' => 'Adidas',
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
            'ngay_tao' => $ngay_tao_2
        ]);

        DB::table('thuong_hieu')->insert([
            'id' => $faker->uuid(),
            'ma_thuong_hieu' => 'TH0003',
            'ten_thuong_hieu' => 'New Balance',
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
            'ngay_tao' => $ngay_tao_3
        ]);
    }
}
