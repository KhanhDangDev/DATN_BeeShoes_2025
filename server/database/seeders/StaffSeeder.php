<?php

namespace Database\Seeders;

use App\Constants\CommonStatus;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use App\Models\Staff;


class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 5; $i++) {
            Staff::create([
                'id' => $faker->uuid,
                'ma_nhan_vien' => 'NV' . $faker->unique()->randomNumber(3, true),
                'ten_nhan_vien' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'mat_khau' => '123456',
                'so_dien_thoai' => $faker->phoneNumber,
                'gioi_tinh' => $faker->randomElement([0, 1]),
                'ngay_sinh' => $faker->dateTimeBetween('-10 years', 'now'),
                'trang_thai' => CommonStatus::DANG_HOAT_DONG,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
