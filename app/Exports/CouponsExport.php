<?php
namespace App\Exports;

use App\Models\Coupon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CouponsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Coupon::with(['couponable'])
            ->get()
            ->map(function ($coupon) {
                return [
                    'ID' => $coupon->id,
                    'Code' => $coupon->code,
                    'Discount' => $coupon->coupon_discount,
                    'Discount Type' => $coupon->discount_type,
                    'Course Title' => $coupon->couponable ? $coupon->couponable->course_title : 'N/A',
                    'Valid Until' => $coupon->coupon_validity,
                    'Status' => $coupon->status ? 'Active' : 'Inactive',
                    'Created At' => $coupon->created_at ? $coupon->created_at->format('Y-m-d H:i:s') : 'N/A',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Code',
            'Discount',
            'Discount Type',
            'Course Title',
            'Valid Until',
            'Status',
            'Created At',
        ];
    }
}