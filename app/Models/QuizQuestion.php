<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $fillable = ['quiz_id', 'question_text', 'options', 'correct_answer'];

    protected $casts = [
        'options' => 'array', // Les options sont stockées sous forme de JSON
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}