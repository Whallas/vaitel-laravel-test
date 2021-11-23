<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::firstOrCreate(['name' => 'account_owner']);

        /** @var Role $role */
        $role = Role::firstOrCreate(['name' => 'account_user']);

        foreach (['customer', 'number', 'number_preference'] as $model) {
            $permissions = [
                Permission::firstOrCreate(['name' => "$model.view"]),
                Permission::firstOrCreate(['name' => "$model.create"]),
                Permission::firstOrCreate(['name' => "$model.update"]),
            ];
            $role->syncPermissions($permissions);
        }
    }
}
