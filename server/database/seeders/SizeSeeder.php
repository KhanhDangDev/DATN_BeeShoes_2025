<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Constants\CommonStatus;
use App\Models\Product;
use Faker\Factory as Faker;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $id_san_pham = Product::pluck('id')->toArray();

        $faker = Faker::create();

        DB::table('kich_co')->insert([
            'id' => $faker->uuid(),
            'id_san_pham' => $faker->randomElement($id_san_pham),
            'ten_kich_co' => '38',
            'so_luong_ton' => 10,
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
        ]);

        DB::table('kich_co')->insert([
            'id' => $faker->uuid(),
            'id_san_pham' => $faker->randomElement($id_san_pham),
            'ten_kich_co' => '39',
            'so_luong_ton' => 14,
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
        ]);

        DB::table('kich_co')->insert([
            'id' => $faker->uuid(),
            'id_san_pham' => $faker->randomElement($id_san_pham),
            'ten_kich_co' => '40',
            'so_luong_ton' => 20,
            'trang_thai' => CommonStatus::DANG_HOAT_DONG,
        ]);
    }
}
