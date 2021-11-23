<?php

namespace App\Policies;

use App\Models\Number;
use App\Models\User;

class NumberPolicy
{
    public function viewAny(User $user)
    {
        return $user->is_account_owner || $user->hasPermissionTo('number.view');
    }

    public function create(User $user)
    {
        return $user->is_account_owner || $user->hasAllPermissions('number.create', 'number.view');
    }

    public function update(User $user, Number $number)
    {
        return $user->account_id == $number->account_id && (
            $user->is_account_owner || $user->hasAllPermissions('number.update', 'number.view')
        );
    }

    public function delete(User $user, Number $number)
    {
        return $this->update($user, $number);
    }

    public function restore(User $user, Number $number)
    {
        return $this->update($user, $number);
    }
}
