<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user)
    {
        return $user->is_account_owner || $user->hasPermissionTo('customer.view');
    }

    public function create(User $user)
    {
        return $user->is_account_owner || $user->hasAllPermissions('customer.create', 'customer.view');
    }

    public function update(User $user, Customer $customer)
    {
        return $user->account_id == $customer->account_id && (
            $user->is_account_owner || $user->hasAllPermissions('customer.update', 'customer.view')
        );
    }

    public function delete(User $user, Customer $customer)
    {
        return $this->update($user, $customer);
    }

    public function restore(User $user, Customer $customer)
    {
        return $this->update($user, $customer);
    }
}
