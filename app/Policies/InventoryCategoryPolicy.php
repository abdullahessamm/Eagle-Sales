<?php

namespace App\Policies;

use App\Models\InventoryCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryCategoryPolicy
{
    use HandlesAuthorization;

    // create policy for create inventory category
    public function manage(User $user)
    {
        if (! $user->isAdmin())
            return false;

        // get user permissions
        $permission = (bool) $user->userInfo->permissions->app_config_access;

        return $permission;
    }
}
