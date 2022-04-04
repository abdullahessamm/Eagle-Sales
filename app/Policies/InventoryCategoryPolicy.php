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
        if ($user->job !== User::ADMIN_JOB_NUMBER)
            return false;

        // get user permissions
        $permissions = $user->userInfo->permissions;
        // get user permissions for inventory categories
        $inventoryCategoriesPermissions = $permissions->categories_access_level;

        // check if user has permission to create inventory categories
        $createInventoryCategoriesPermission = (bool) substr($inventoryCategoriesPermissions, 0, 1);
        return $createInventoryCategoriesPermission;
    }
}
