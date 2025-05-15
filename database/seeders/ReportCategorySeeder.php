<?php

namespace Database\Seeders;

use App\Models\ReportCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReportCategorySeeder extends Seeder
{
  public function run()
  {
    $categories = [
      ['name' => 'Course Issue', 'description' => 'Issues related to course content or delivery'],
      ['name' => 'Technical', 'description' => 'Technical problems with the platform'],
      ['name' => 'Content Error', 'description' => 'Errors in course content'],
      ['name' => 'Billing', 'description' => 'Issues related to payments or billing'],
      ['name' => 'Other', 'description' => 'Miscellaneous issues'],
    ];

    foreach ($categories as $category) {
      ReportCategory::create([
        'name' => $category['name'],
        'slug' => Str::slug($category['name']),
        'description' => $category['description'],
        'is_active' => true,
      ]);
    }
  }
}
