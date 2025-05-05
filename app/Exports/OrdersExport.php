<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::with(['user', 'course', 'instructor'])
            ->get()
            ->map(function ($order) {
                return [
                    'ID' => $order->id,
                    'User Name' => $order->user->name,
                    'Course Title' => $order->course->course_title,
                    'Instructor Name' => $order->instructor->name,
                    'Price' => $order->price,
                    'Order Date' => $order->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'Course Title',
            'Instructor Name',
            'Price',
            'Order Date',
        ];
    }
}