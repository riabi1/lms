<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'admin.roles.index',
            'admin.roles.assign',
            'admin.roles.create',
            'admin.permissions.manage',
            'admin.categories.index',
            'admin.categories.create',
            'admin.categories.store',
            'admin.categories.edit',
            'admin.categories.update',
            'admin.categories.destroy',
            'admin.subcategories.index',
            'admin.subcategories.create',
            'admin.subcategories.store',
            'admin.subcategories.edit',
            'admin.subcategories.update',
            'admin.subcategories.destroy',
            'admin.courses.index',
            'admin.courses.updateStatus',
            'admin.instructors.index',
            'admin.instructors.updateStatus',
            'admin.instructors.downloadCv',
            'admin.pending.review',
            'admin.active.review',
            'admin.update.review.status',
            'admin.orders.index',
            'admin.orders.show',
            'admin.coupon.index',
            'admin.coupon.show',
            'admin.users.index',
            'admin.site.settings',
            'admin.site.update',
            'admin.blog-categories.index',
            'admin.blog-categories.create',
            'admin.blog-categories.store',
            'admin.blog-categories.edit',
            'admin.blog-categories.update',
            'admin.blog-categories.destroy',
            'admin.blog-posts.index',
            'admin.blog-posts.toggle',
            'admin.comments.index',
            'admin.comments.toggle',
            'admin.comments.destroy',
            'admin.earnings',
            'admin.reports.index',
            'admin.reports.update',
            'admin.report-categories.index',
            'admin.report-categories.create',
            'admin.report-categories.store',
            'admin.report-categories.edit',
            'admin.report-categories.update',
            'admin.report-categories.destroy',
            'admin.excel.index',
            'admin.excel.enrollments',
            'admin.excel.payments',
            'admin.excel.users',
            'admin.excel.instructors',
            'admin.excel.orders',
            'admin.excel.courses',
            'admin.excel.admins',
            'admin.excel.blog-posts',
            'admin.excel.blog-categories',
            'admin.excel.coupons',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }
    }
}