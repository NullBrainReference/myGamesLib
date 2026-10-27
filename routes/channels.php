<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('chat.{id}', function (User $user, int $id) {
    return (int) $user->id === (int) $id;
});
