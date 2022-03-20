<?php

namespace App\Observers;

use App\Models\BackOfficeUser;

class BackofficeUserObserver
{

    public function created(BackOfficeUser $backOfficeUser)
    {
        $permissions = new \App\Models\Permission;
        $permissions->backoffice_user_id = $backOfficeUser->id;
        $permissions->suppliers_access_level = '0000';
        $permissions->customers_access_level = '0000';
        $permissions->sellers_access_level = '0000';
        $permissions->categorys_access_level = '0000';
        $permissions->items_access_level = '0000';
        $permissions->backoffice_emps_access_level = '0000';
        $permissions->orders_access_level = '0000';
        $permissions->commissions_access_level = '0000';
        $permissions->journey_plan_access_level = '0000';
        $permissions->pricelists_access_level = '0000';
        $permissions->app_config_access = false;
        $permissions->save();
    }

    /**
     * Handle the BackOfficeUser "updated" event.
     *
     * @param  \App\Models\BackOfficeUser  $backOfficeUser
     * @return void
     */
    public function updated(BackOfficeUser $backOfficeUser)
    {
        $user = $backOfficeUser->getUser();
        $user->updated_at = now();
        if (auth()->user())
            $user->updated_by = auth()->user()->userData->id;
        $user->save();
    }
}
