<?php

namespace Database\Seeders;

use App\Constants\CommonStatus;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;


class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        for ($i = 0; $i < 5; $i++) {
            Customer::create([
                'id' => $faker->uuid,
                'ma_khach_hang' => 'KH' . $faker->unique()->randomNumber(3, true),
                'ten_khach_hang' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'so_dien_thoai' => $faker->phoneNumber,
                'gioi_tinh' => $faker->randomElement([0, 1]),
                'ngay_sinh' => $faker->dateTimeBetween('-10 years', 'now'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
