<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvailableCityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function change(User $user)
    {
        if ($user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        return (bool) $user->userInfo->permissions->app_config_access;
    }

}
