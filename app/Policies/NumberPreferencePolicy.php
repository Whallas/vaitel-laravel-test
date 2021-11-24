<?php

namespace App\Policies;

use App\Models\User;

class NumberPreferencePolicy
{
    public function create(User $user)
    {
        return $user->is_account_owner || $user->hasPermissionTo('number_preference.create');
    }

    public function update(User $user)
    {
        return $user->is_account_owner || $user->hasPermissionTo('number_preference.update');
    }

    public function delete(User $user)
    {
        return $this->update($user);
    }
}
