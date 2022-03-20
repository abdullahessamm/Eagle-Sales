<?php

namespace App\Observers;

use App\Models\Permission;

class PermissionsObserver
{

    /**
     * Handle the Permission "updated" event.
     *
     * @param  \App\Models\Permission  $permission
     * @return void
     */
    public function updated(Permission $permission)
    {
        $backofficeUser = $permission->getBackofficeUser();
        $backofficeUser->updated_at = now();
        $backofficeUser->save();
    }
}
