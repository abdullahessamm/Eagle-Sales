<?php

namespace App\Policies;

use App\Models\JourneyPlan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JourneyPlanPolicy
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

        $permissions = $user->userInfo->permissions->journey_plan_access_level;
        return (bool) substr($permissions, 1, 1);
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

        $permissions = $user->userInfo->permissions->journey_plan_access_level;
        return (bool) substr($permissions, 0, 1);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\JourneyPlan  $journeyPlan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        if (! $user->isAdmin())
            return false;

        $permissions = $user->userInfo->permissions->journey_plan_access_level;
        return (bool) substr($permissions, 2, 1);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\JourneyPlan  $journeyPlan
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user)
    {
        if (! $user->isAdmin())
            return false;

        $permissions = $user->userInfo->permissions->journey_plan_access_level;
        return (bool) substr($permissions, 3, 1);
    }
}
