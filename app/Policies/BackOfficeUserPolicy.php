<?php

namespace App\Policies;

use App\Models\BackOfficeUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BackOfficeUserPolicy
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

        $backofficeUsersPermissions = $user->userInfo->permissions->backoffice_emps_access_level;
        return (bool) substr($backofficeUsersPermissions, 1, 1);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BackOfficeUser  $backOfficeUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, BackOfficeUser $backOfficeUser)
    {
        if ($user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        $backofficeUsersPermissions = $user->userInfo->permissions->backoffice_emps_access_level;
        $backOfficeUserId = $user->userInfo->id;
        
        return (bool) substr($backofficeUsersPermissions, 1, 1) || $backOfficeUserId === $backOfficeUser->id;
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
        
        $backofficeUsersPermissions = $user->userInfo->permissions->backoffice_emps_access_level;
        return (bool) substr($backofficeUsersPermissions, 0, 1);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BackOfficeUser  $backOfficeUser
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        if ($user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        $backofficeUsersPermissions = $user->userInfo->permissions->backoffice_emps_access_level;
        return (bool) substr($backofficeUsersPermissions, 2, 1);
    }

    public function ban(User $user)
    {
        return $this->update($user);
    }

    public function approve(User $user)
    {
        return $this->update($user);
    }
}
