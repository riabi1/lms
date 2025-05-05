<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;
use App\Models\User;
use App\Models\Instructor;


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    return $user->id === Conversation::findOrFail($conversationId)->user_id ||
           $user->id === Conversation::findOrFail($conversationId)->instructor_id;
});