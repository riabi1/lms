<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cartable_type',
        'cartable_id',
        'user_id',
        'price',
        'quantity',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartable()
    {
        return $this->morphTo();
    }
}
