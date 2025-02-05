<?php

namespace Database\Seeders;

use App\Constants\CommonStatus;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class MaterialSeeder extends Seeder
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

        DB::table('chat_lieu')->insert([
            'id' => $faker->uuid(),
            'ma_chat_lieu' => 'CL0001',
            'ten_chat_lieu' => 'Da',
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
            'ngay_tao' => $ngay_tao_1
        ]);

        DB::table('chat_lieu')->insert([
            'id' => $faker->uuid(),
            'ma_chat_lieu' => 'CL0002',
            'ten_chat_lieu' => 'Vải',
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
            'ngay_tao' => $ngay_tao_2
        ]);

        DB::table('chat_lieu')->insert([
            'id' => $faker->uuid(),
            'ma_chat_lieu' => 'CL0003',
            'ten_chat_lieu' => 'Da lộn',
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
            'ngay_tao' => $ngay_tao_3
        ]);
    }
}
