<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'PCS',     'name' => 'Pcs'],
            ['code' => 'PORSI',   'name' => 'Porsi'],
            ['code' => 'CUP',     'name' => 'Cup'],
            ['code' => 'GELAS',   'name' => 'Gelas'],
            ['code' => 'BOTOL',   'name' => 'Botol'],
            ['code' => 'BUNGKUS', 'name' => 'Bungkus'],
            ['code' => 'PAKET',   'name' => 'Paket'],
            ['code' => 'BOX',     'name' => 'Box'],
            ['code' => 'PIRING',  'name' => 'Piring'],
            ['code' => 'MANGKOK', 'name' => 'Mangkok'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(
                ['txtUnitCode' => $unit['code']],
                [
                    'txtUnitName' => $unit['name'],
                    'bitActive' => 1,
                    'txtInsertedBy' => 'SYSTEM',
                    'txtUpdatedBy' => 'SYSTEM',
                ]
            );
        }
    }
}

