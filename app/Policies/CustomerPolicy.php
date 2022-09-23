<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        if ($user->job !== User::ADMIN_JOB_NUMBER)
            return false;
        
        $userPermissions = $user->userInfo->permissions;
        $createCustomerPermission = (bool) substr($userPermissions->customers_access_level, 1, 1);
        return $createCustomerPermission;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Customer $customer)
    {
        if ($user->job !== User::CUSTOMER_JOB_NUMBER && $user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        if ($user->job === User::ADMIN_JOB_NUMBER) {
            $userPermissions = $user->userInfo->permissions;
            $readCustomerPermission = (bool) substr($userPermissions->customers_access_level, 1, 1);
            return $readCustomerPermission;
        }

        if ($user->job === User::CUSTOMER_JOB_NUMBER)
            return $user->userInfo->id === $customer->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if (! $user->isAdmin())
            return false;

        $userPermissions = $user->userInfo->permissions;
        $createCustomerPermission = (bool) substr($userPermissions->customers_access_level, 0, 1);
        return $createCustomerPermission;
    }

    public function updateFullData(User $user, Customer $customer)
    {
        if ($user->job !== User::CUSTOMER_JOB_NUMBER && $user->job !== User::ADMIN_JOB_NUMBER)
            return false;
        
        if ($user->job === User::ADMIN_JOB_NUMBER) {
            $userPermissions = $user->userInfo->permissions;
            $updateCustomerPermission = (bool) substr($userPermissions->customers_access_level, 2, 1);
            return $updateCustomerPermission;
        }

        if ($user->job === User::CUSTOMER_JOB_NUMBER)
            return $user->userInfo->id === $customer->id && ! (bool) $user->is_approved;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Customer $customer)
    {
        if ($user->job !== User::CUSTOMER_JOB_NUMBER)
            return false;

        return $user->userInfo->id === $customer->id;
    }

    public function ban(User $user)
    {
        return $this->create($user);
    }

    public function approve(User $user)
    {
        return $this->create($user);
    }
}
