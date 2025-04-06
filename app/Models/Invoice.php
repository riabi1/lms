<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_number',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'payment_id',
        'items',
        'issued_at',
    ];

    protected $casts = [
        'items' => 'array',
        'issued_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}