<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;
use App\Models\Reminder;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReminderPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Card $card)
    {
        return $card->users()->where('user_id', $user->id)->exists();
    }
    public function update(User $user, Reminder $reminder)
    {
        return $reminder->card->users()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, Reminder $reminder)
    {
        return $reminder->card->users()->where('user_id', $user->id)->exists();
    }
}