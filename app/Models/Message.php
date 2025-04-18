<?php

namespace App\Models;

use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages'; // Par défaut, mais vérifie qu’il n’a pas été modifié

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'message', // Assure-toi que cette colonne est bien listée ici
        'read_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'created' => MessageSent::class,
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->morphTo();
    }
}