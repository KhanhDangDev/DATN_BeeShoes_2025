<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ColorSeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(MaterialSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(SizeSeeder::class);
    }
}
