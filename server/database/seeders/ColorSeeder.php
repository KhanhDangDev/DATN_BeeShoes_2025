<?php

namespace Database\Seeders;

use App\Constants\CommonStatus;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;


class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $ngay_tao_1 = Carbon::now()->setTimezone('Asia/Ho_Chi_Minh'); // lấy múi giờ hiện tại
        $ngay_tao_2 = Carbon::parse($ngay_tao_1)->addMinutes(1);
        $ngay_tao_3 = Carbon::parse($ngay_tao_2)->addMinutes(1);
        // $ngay_tao_4 = Carbon::parse($ngay_tao_3)->addMinutes(1);

        DB::table('mau_sac')->insert([
            'id' => $faker->uuid(),
            'ma_mau_sac' => 'MS0001',
            'ten_mau_sac' => 'Màu đỏ',
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
            'ngay_tao' => $ngay_tao_1
        ]);

        DB::table('mau_sac')->insert([
            'id' => $faker->uuid(),
            'ma_mau_sac' => 'MS0002',
            'ten_mau_sac' => 'Màu cam',
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
            'ngay_tao' => $ngay_tao_2
        ]);

        DB::table('mau_sac')->insert([
            'id' => $faker->uuid(),
            'ma_mau_sac' => 'MS0003',
            'ten_mau_sac' => 'Màu xanh',
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
            'ngay_tao' => $ngay_tao_3
        ]);
    }
}
