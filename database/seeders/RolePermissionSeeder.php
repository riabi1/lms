<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create Admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        // Assign all permissions to Admin
        $adminRole->syncPermissions(Permission::all());

        // Create Sub-Admin role
        $subAdminRole = Role::firstOrCreate(['name' => 'sub-admin', 'guard_name' => 'admin']);
        // Assign default permissions to Sub-Admin
        $subAdminPermissions = [
            'admin.courses.index',
            'admin.courses.updateStatus',
            'admin.instructors.index',
            'admin.instructors.updateStatus',
            'admin.instructors.downloadCv',
            'admin.pending.review',
            'admin.active.review',
            'admin.update.review.status',
            'admin.blog-posts.index',
            'admin.blog-posts.toggle',
        ];
        $existingPermissions = Permission::whereIn('name', $subAdminPermissions)->pluck('name')->toArray();
        $subAdminRole->syncPermissions($existingPermissions);
    }
}
