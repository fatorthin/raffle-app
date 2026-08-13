<?php

namespace Database\Seeders;

use App\Models\Prize;
use Illuminate\Database\Seeder;

class PrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prizes = [
            // Hadiah Utama
            [
                'name' => 'Sepeda Gunung Polygon Premier',
                'quota' => 2,
                'image_path' => null,
            ],
            [
                'name' => 'Smart TV 43 Inch 4K UHD',
                'quota' => 1,
                'image_path' => null,
            ],
            [
                'name' => 'Kulkas 2 Pintu Inverter',
                'quota' => 1,
                'image_path' => null,
            ],
            [
                'name' => 'Mesin Cuci Otomatis 8 Kg',
                'quota' => 1,
                'image_path' => null,
            ],
            [
                'name' => 'Sepeda Lipat Exotic',
                'quota' => 2,
                'image_path' => null,
            ],

            // Hadiah Elektronik & Rumah Tangga
            [
                'name' => 'Air Fryer Digital 4L',
                'quota' => 3,
                'image_path' => null,
            ],
            [
                'name' => 'Magic Com Digital 2L',
                'quota' => 4,
                'image_path' => null,
            ],
            [
                'name' => 'Kipas Angin Stand Fan 16 Inch',
                'quota' => 6,
                'image_path' => null,
            ],
            [
                'name' => 'Kompor Gas 2 Tungku Stainless',
                'quota' => 3,
                'image_path' => null,
            ],
            [
                'name' => 'Dispenser Galon Bawah',
                'quota' => 2,
                'image_path' => null,
            ],
            [
                'name' => 'Blender Glass 3-in-1',
                'quota' => 5,
                'image_path' => null,
            ],
            [
                'name' => 'Setrika Listrik Heavy Duty',
                'quota' => 10,
                'image_path' => null,
            ],

            // Hadiah Hiburan & Merchandise
            [
                'name' => 'Payung Eksklusif Jalan Sehat',
                'quota' => 20,
                'image_path' => null,
            ],
            [
                'name' => 'Tumbler Termos Stainless 500ml',
                'quota' => 25,
                'image_path' => null,
            ],
            [
                'name' => 'Paket Sembako Spesial',
                'quota' => 15,
                'image_path' => null,
            ],
        ];

        foreach ($prizes as $prize) {
            Prize::create($prize);
        }
    }
}
