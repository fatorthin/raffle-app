<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $now = now();
        $totalCoupons = 1000;
        $chunkSize = 200;
        $coupons = [];

        for ($i = 1; $i <= $totalCoupons; $i++) {
            $coupons[] = [
                'coupon_number' => 'JLS-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'name' => $faker->name(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($coupons) === $chunkSize) {
                Coupon::insert($coupons);
                $coupons = [];
            }
        }

        if (!empty($coupons)) {
            Coupon::insert($coupons);
        }
    }
}
