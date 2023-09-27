<?php
declare(strict_types=1);


namespace CSCart\Lunar\Blog\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'blog-post:create']);
        Permission::create(['name' => 'blog-post:update']);
        Permission::create(['name' => 'blog-post:delete']);
        Permission::create(['name' => 'blog-post:read']);
        Permission::create(['name' => 'blog-post:manage']);
        Permission::create(['name' => 'blog-category:manage']);

        $role = Role::create(['name' => 'blog-writer']);
        $role->givePermissionTo(['blog-post:create', 'blog-post:update', 'blog-post:delete', 'blog-post:read']);

        $role = Role::create(['name' => 'blog-admin']);
        $role->givePermissionTo(['blog-post:manage', 'blog-category:manage']);
    }
}
