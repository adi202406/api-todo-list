<?php

namespace App\Policies;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReminderPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Reminder $reminder)
    {
        return $reminder->card->users()->where('user_id', $user->id)->exists();
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