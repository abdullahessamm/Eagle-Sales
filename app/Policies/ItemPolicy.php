<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Item $item)
    {
        // if user is not supplier or admin return false
        if ($user->job !== User::SUPPLIER_JOB_NUMBER && $user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        if ($user->job === User::ADMIN_JOB_NUMBER) {
            $userPermissions = $user->userInfo->permissions;
            return (bool) substr($userPermissions->items_access_level, 1, 1);
        }

        // if user is supplier return item supplier_id === user info id
        return $item->supplier_id === $user->userInfo->id;
    }

    /**
     *
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->job !== User::SUPPLIER_JOB_NUMBER)
            return false;

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Item $item)
    {
        if ($user->job !== User::SUPPLIER_JOB_NUMBER || $user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        if ($user->job === User::ADMIN_JOB_NUMBER) {
            $userPermissions = $user->userInfo->permissions;
            return (bool) substr($userPermissions->items_access_level, 2, 1);
        }

        return (bool) ((int) $item->supplier_id === (int) $user->userInfo->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deactivate(User $user, Item $item)
    {
        if ($user->job !== User::SUPPLIER_JOB_NUMBER)
            return false;

        return (bool) ((int) $item->supplier_id === (int) $user->userInfo->id);
    }

    public function approve(User $user)
    {
        if ($user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        $userPermissions = $user->userInfo->permissions;
        return (bool) substr($userPermissions->items_access_level, 2, 1);
    }

    public function rateOrComment(User $user)
    {
        if ($user->job === User::CUSTOMER_JOB_NUMBER)
            return true;

        return false;
    }
}
