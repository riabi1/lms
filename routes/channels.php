<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::findOrFail($conversationId);

    // Check if the user is either the user_id or instructor_id in the conversation
    $guard = Auth::guard('instructor')->check() ? 'instructor' : 'web';
    $userId = Auth::guard($guard)->id();

    return $conversation->user_id === $userId || $conversation->instructor_id === $userId;
});