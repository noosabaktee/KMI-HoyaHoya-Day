<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['code' => 'MFG', 'name' => 'Manufacturing'],
            ['code' => 'DNP', 'name' => 'Development & Planning'],
            ['code' => 'BDA', 'name' => 'Business Development Analysis'],
            ['code' => 'HC',  'name' => 'Human Capital'],
            ['code' => 'ENG', 'name' => 'Engineering'],
            ['code' => 'QAQC', 'name' => 'QA / QC'],
            ['code' => 'WHS', 'name' => 'Warehouse'],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['txtDepartmentCode' => $dept['code']],
                [
                    'txtDepartmentName' => $dept['name'],
                    'bitActive' => 1,
                    'txtInsertedBy' => 'SYSTEM',
                    'txtUpdatedBy' => 'SYSTEM',
                ]
            );
        }
    }
}

