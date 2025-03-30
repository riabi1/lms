<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class, 'user_id' ,'id');
    }

   

  public function courses()
    {
        return $this->morphMany(Course::class, 'courseable');
    }

public function instructor(){
        return $this->belongsTo(Instructor::class, 'instructor_id', 'id');
    }

}