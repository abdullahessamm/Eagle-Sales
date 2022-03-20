<?php

namespace App\Policies;

use App\Models\CustomerCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerCategoryPloicy
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
        $permissions = $user->userInfo->permissions;
        return (bool) substr($permissions->categorys_access_level, 1, 1);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CustomerCategory  $customerCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {
        $permissions = $user->userInfo->permissions;
        return (bool) substr($permissions->categorys_access_level, 1, 1);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        $permissions = $user->userInfo->permissions;
        return (bool) substr($permissions->categorys_access_level, 0, 1);
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
        $permissions = $user->userInfo->permissions;
        return (bool) substr($permissions->categorys_access_level, 2, 1);
    }
}
