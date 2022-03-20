<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
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
        $readSellerPermission = (bool) substr($userPermissions->suppliers_access_level, 1, 1);
        return $readSellerPermission;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Supplier $supplier)
    {
        if ($user->job !== User::SUPPLIER_JOB_NUMBER && $user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        if ($user->job === User::ADMIN_JOB_NUMBER) {
            $userPermissions = $user->userInfo->permissions;
            $readSellerPermission = (bool) substr($userPermissions->suppliers_access_level, 1, 1);
            return $readSellerPermission;
        }

        if ($user->job === User::SUPPLIER_JOB_NUMBER)
            return $user->userInfo->id === $supplier->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        $userPermissions = $user->userInfo->permissions;
        $createSellerPermission = (bool) substr($userPermissions->suppliers_access_level, 0, 1);
        return $createSellerPermission;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Supplier $supplier)
    {
        if ($user->job !== User::SUPPLIER_JOB_NUMBER && $user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        if ($user->job === User::ADMIN_JOB_NUMBER) {
            $userPermissions = $user->userInfo->permissions;
            $updateSupplierPermission = (bool) substr($userPermissions->suppliers_access_level, 2, 1);
            return $updateSupplierPermission;
        }

        if ($user->job === User::SUPPLIER_JOB_NUMBER)
            return $user->userInfo->id === $supplier->id;
    }
}
