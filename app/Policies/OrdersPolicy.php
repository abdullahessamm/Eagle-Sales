<?php

namespace App\Policies;

use App\Models\AppConfig;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrdersPolicy
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

        $permissions = $user->userInfo->permissions->orders_access_level;
        $canRead = (bool) substr($permissions, 1, 1);
        return $canRead;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Order $order)
    {
        if ($user->isSupplier())
            return (int) $user->userInfo->id === (int) $order->supplier_id;
        return (int) $user->id === (int) $order->buyer_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->isCustomer() || $user->isOnlineClient() || $user->isSeller();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Order $order)
    {
        if ($user->isSupplier())
            return (int) $user->userInfo->id === (int) $order->supplier_id;

        if ($user->isAdmin())
            return (bool) substr($user->userInfo->permissions->orders_access_level, 2, 1);
    }

    public function cancel(User $user, Order $order)
    {
        if ($order->isApproved())
        {
            $orderCanCancel = AppConfig::orderCanBeCancelledAfterApproved();
            if (! $orderCanCancel)
                return false;
        }

        if ($user->isSupplier())
            return (int) $user->userInfo->id === (int) $order->supplier_id;

        return (int) $user->id === (int) $order->buyer_id;
    }
}
