<?php

namespace Chronos\Menu\Seeds;

use Chronos\Scaffolding\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name' => 'manage_menus',
            'label' => trans('chronos.menu::permissions.Manage menus'),
            'order' => 10
        ]);
    }

}