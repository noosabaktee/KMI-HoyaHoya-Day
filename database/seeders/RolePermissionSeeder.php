<?php

namespace Database\Seeders;

use App\Services\MenuPermissionMap;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        MenuPermissionMap::ensureSystemPermissionsExist();

        $administrator = Role::findOrCreate('Administrator', 'web');
        $outlet = Role::findOrCreate('Outlet', 'web');
        $cashier = Role::findOrCreate('Kasir', 'web');

        $administrator->syncPermissions(Permission::all());
        $cashier->syncPermissions(MenuPermissionMap::permissionsForMenus(['pos']));
        $outlet->syncPermissions(MenuPermissionMap::permissionsForMenus(['dashboard', 'analytic', 'laporan', 'biaya', 'crm']));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
