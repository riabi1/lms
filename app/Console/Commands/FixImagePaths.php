<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;

class FixImagePaths extends Command
{
  protected $signature = 'fix:image-paths';
  protected $description = 'Convert full image URLs to relative paths';

  public function handle()
  {
    $categories = Category::all();

    foreach ($categories as $category) {
      if (str_starts_with($category->image, 'http://localhost/storage/')) {
        $relativePath = str_replace('http://localhost/storage/', '', $category->image);
        $category->image = $relativePath;
        $category->save();
        $this->info("Fixed: {$category->category_name} -> {$relativePath}");
      }
    }

    $this->info('Image paths fixed!');
  }
}
