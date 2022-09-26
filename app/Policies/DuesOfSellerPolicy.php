<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DuesOfSellerPolicy
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
        if (! $user->isAdmin())
            return false;

        $userPermissions  = $user->userInfo->permissions;
        $commissionAccess = $userPermissions->commissions_access_level;

        return (bool) substr($commissionAccess, 1, 1);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\OurCommission  $ourCommission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function withdraw(User $user)
    {
        if (! $user->isAdmin())
            return false;

        $userPermissions  = $user->userInfo->permissions;
        $commissionAccess = $userPermissions->commissions_access_level;

        return (bool) substr($commissionAccess, 2, 1);
    }
}
