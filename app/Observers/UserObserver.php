<?php

namespace App\Observers;

class UserObserver
{
    public function creating(User $user)
    {
        $user->password = Hash::make($user->password);
    }
}
