<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CoursesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Course::with(['subcategory'])
            ->get()
            ->map(function ($course) {
                return [
                    'ID' => $course->id,
                    'Title' => $course->course_title,
                    'Subcategory' => $course->subcategory->subcategory_name,
                    'Selling Price' => $course->selling_price,
                    'Discount Price' => $course->discount_price ?? 'N/A',
                    'Duration' => $course->duration,
                    'Status' => $course->status ? 'Active' : 'Inactive',
                    'Created At' => $course->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Subcategory',
            'Selling Price',
            'Discount Price',
            'Duration',
            'Status',
            'Created At',
        ];
    }
}