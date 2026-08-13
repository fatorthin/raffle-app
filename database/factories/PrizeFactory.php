<?php

namespace Database\Factories;

use App\Models\Prize;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prize>
 */
class PrizeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Sepeda Gunung Polygon',
                'Kulkas 2 Pintu Sharp',
                'Smart TV 43 Inch Samsung',
                'Mesin Cuci LG',
                'Smartphone Xiaomi',
                'Air Fryer Philips',
                'Magic Com Miyako',
                'Kipas Angin Cosmos',
                'Dispenser Galon Bawah',
                'Setrika Listrik Maspion',
                'Blender Philips',
                'Payung Eksklusif Jalan Sehat',
                'Tumbler Stainless Steel',
                'Kompor Gas 2 Tungku Rinnai',
                'Voucher Belanja Rp 500.000',
            ]),
            'quota' => fake()->numberBetween(1, 5),
            'image_path' => null,
        ];
    }
}
