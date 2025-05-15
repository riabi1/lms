<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reporter_type',
        'course_id',
        'report_category_id',
        'title',
        'type',
        'description',
        'status',
        'resolution_notes',
    ];

    public function reporter()
    {
        return $this->morphTo();
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

  public function reportCategory()
  {
    return $this->belongsTo(ReportCategory::class);
  }
}