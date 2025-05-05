<?php

namespace App\Exports;

use App\Models\Instructor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InstructorsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Instructor::select('id', 'name', 'email', 'phone', 'address', 'created_at')
            ->get()
            ->map(function ($instructor) {
                return [
                    'ID' => $instructor->id,
                    'Name' => $instructor->name,
                    'Email' => $instructor->email,
                    'Phone' => $instructor->phone ?? 'N/A',
                    'Address' => $instructor->address ?? 'N/A',
                    'Created At' => $instructor->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Address',
            'Created At',
        ];
    }
}