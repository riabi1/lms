<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'coupon_discount',
        'discount_type',
        'max_uses',
        'uses',
        'coupon_validity',
        'status',
        'couponable_id',
        'couponable_type',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'coupon_validity' => 'date',
        'coupon_discount' => 'decimal:2',
        'status' => 'integer',
    ];

    public function couponable()
    {
        return $this->morphTo();
    }
}