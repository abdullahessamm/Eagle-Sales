<?php

namespace App\Policies;

use App\Models\CustomerCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerCategoryPloicy
{
    use HandlesAuthorization;

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

        // get user permissions
        $permission = (bool) $user->userInfo->permissions->app_config_access;

        return $permission;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CustomerCategory  $customerCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        return $this->create($user);
    }
}
