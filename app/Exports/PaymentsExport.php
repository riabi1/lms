<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::with(['user', 'course'])
            ->get()
            ->map(function ($order) {
                return [
                    'User Name' => $order->user->name,
                    'Course Title' => $order->course->course_title,
                    'Price' => $order->price,
                    'Payment Date' => $order->created_at->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Course Title',
            'Price',
            'Payment Date',
        ];
    }
}