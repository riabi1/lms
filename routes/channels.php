<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('conversation.{messages_id}', function ($user, $id) {
    $conversation = Conversation::findOrFail($id);
    $authorized = $user->id === $conversation->user_id || $user->id === $conversation->instructor_id;
    Log::info('Channel authorization', [
        'user_id' => $user->id,
        'conversation_id' => $id,
        'authorized' => $authorized
    ]);
    return $authorized;
}, ['guards' => ['web', 'instructor']]);