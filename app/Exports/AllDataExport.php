<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllDataExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Enrollments' => new EnrollmentsExport(),
            'Payments' => new PaymentsExport(),
            'Users' => new UsersExport(),
            'Instructors' => new InstructorsExport(),
            'Orders' => new OrdersExport(),
            'Courses' => new CoursesExport(),
        ];
    }
}