<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'mobile_image' => '1.png',
                'desktop_image' => '1.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '2.png',
                'desktop_image' => '2.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '3.png',
                'desktop_image' => '3.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '1.png',
                'desktop_image' => '1.png',
                'agent_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '2.png',
                'desktop_image' => '2.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '3.png',
                'desktop_image' => '3.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '1.png',
                'desktop_image' => '1.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '2.png',
                'desktop_image' => '2.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '3.png',
                'desktop_image' => '3.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '1.png',
                'desktop_image' => '1.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '2.png',
                'desktop_image' => '2.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mobile_image' => '3.png',
                'desktop_image' => '3.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('banners')->insert($banners);
    }
}
