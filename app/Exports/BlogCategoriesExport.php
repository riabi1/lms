<?php
namespace App\Exports;

use App\Models\BlogCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BlogCategoriesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return BlogCategory::select('id', 'name', 'slug', 'created_at')
            ->get()
            ->map(function ($category) {
                return [
                    'ID' => $category->id,
                    'Name' => $category->name,
                    'Slug' => $category->slug,
                    'Created At' => $category->created_at ? $category->created_at->format('Y-m-d H:i:s') : 'N/A',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Slug',
            'Created At',
        ];
    }
}