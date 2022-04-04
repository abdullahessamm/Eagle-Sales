<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellerPolicy
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
        $createSellerPermission = (bool) substr($userPermissions->sellers_access_level, 1, 1);
        return $createSellerPermission;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Seller $seller)
    {
        if ($user->job !== User::HIERD_SELLER_JOB_NUMBER &&
            $user->job !== User::FREELANCER_SELLER_JOB_NUMBER &&
            $user->job !== User::ADMIN_JOB_NUMBER
        ) return false;

        if ($user->job === User::ADMIN_JOB_NUMBER) {
            $userPermissions = $user->userInfo->permissions;
            $createSellerPermission = (bool) substr($userPermissions->sellers_access_level, 1, 1);
            return $createSellerPermission;
        }

        if ($user->job === User::HIERD_SELLER_JOB_NUMBER ||
            $user->job === User::FREELANCER_SELLER_JOB_NUMBER
        ) return $user->userInfo->id === $seller->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if ($user->job !== User::ADMIN_JOB_NUMBER)
            return false;
        
        $userPermissions = $user->userInfo->permissions;
        $createSellerPermission = (bool) substr($userPermissions->sellers_access_level, 0, 1);
        return $createSellerPermission;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Seller $seller)
    {
        if ($user->job !== User::HIERD_SELLER_JOB_NUMBER &&
            $user->job !== User::FREELANCER_SELLER_JOB_NUMBER &&
            $user->job !== User::ADMIN_JOB_NUMBER
        ) return false;

        if ($user->job === User::ADMIN_JOB_NUMBER) {
            $userPermissions = $user->userInfo->permissions;
            $createSellerPermission = (bool) substr($userPermissions->sellers_access_level, 2, 1);
            return $createSellerPermission;
        }

        if ($user->job === User::HIERD_SELLER_JOB_NUMBER ||
            $user->job === User::FREELANCER_SELLER_JOB_NUMBER
        )
        {
            $userAccount = $seller->getUser();
            if ($userAccount->is_approved)
                return false;
            return $user->userInfo->id === $seller->id;
        }
    }

    public function ban(User $user)
    {
        if ($user->job !== User::ADMIN_JOB_NUMBER)
            return false;
        
        $userPermissions = $user->userInfo->permissions;
        $createSellerPermission = (bool) substr($userPermissions->sellers_access_level, 2, 1);
        return $createSellerPermission;
    }

    public function approve(User $user)
    {
        return $this->ban($user);
    }
}
