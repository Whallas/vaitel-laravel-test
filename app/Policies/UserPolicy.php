<?php

namespace App\Policies;

use App\Models\Number;
use App\Models\User;

class UserPolicy
{
    public function before(User $user)
    {
        return $user->is_account_owner;
    }

    public function viewAny(User $user)
    {
        return false;
    }

    public function create(User $user)
    {
        return false;
    }

    public function update(User $user, Number $number)
    {
        return false;
    }

    public function delete(User $user, Number $number)
    {
        return false;
    }

    public function restore(User $user, Number $number)
    {
        return false;
    }
}
