<?php
namespace App\Exports;

use App\Models\BlogPost;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BlogPostsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return BlogPost::with(['instructor', 'category'])
            ->get()
            ->map(function ($post) {
                return [
                    'ID' => $post->id,
                    'Title' => $post->title,
                    'Category' => $post->category ? $post->category->name : 'N/A', // Changed from blogCategory to category
                    'Instructor' => $post->instructor ? $post->instructor->name : 'N/A',
                    'Status' => $post->status,
                    'Created At' => $post->created_at ? $post->created_at->format('Y-m-d H:i:s') : 'N/A',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Category',
            'Instructor',
            'Status',
            'Created At',
        ];
    }
}