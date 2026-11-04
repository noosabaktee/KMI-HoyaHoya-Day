<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Makanan Utama',
            'Makanan Ringan / Snack',
            'Minuman Dingin & Hangat',
            'Dessert & Takjil',
            'Kopi & Teh',
            'Paket Combo / Hemat',
        ];

        foreach ($categories as $catName) {
            Category::updateOrCreate(
                [
                    'intMerchant_ID' => null,
                    'txtCategoryName' => $catName,
                ],
                [
                    'txtCategorySlug' => Str::slug($catName),
                    'bitActive' => 1,
                    'txtInsertedBy' => 'SYSTEM',
                    'txtUpdatedBy' => 'SYSTEM',
                ]
            );
        }
    }
}
