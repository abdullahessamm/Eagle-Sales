<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvailableCountryPolicy
{
    use HandlesAuthorization;

    public function change(User $user)
    {
        if ($user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        return (bool) $user->userInfo->permissions->app_config_access;
    }
}
