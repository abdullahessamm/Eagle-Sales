<?php

namespace App\Events\Accounts;

use App\Models\Customer;
use App\Models\Permission;
use App\Models\Seller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class NewUserArrived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    protected array $listeners;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->listeners = $this->getListeners();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $listeners = $this->listeners;
        $channels = [];
        foreach ($listeners as $listener) {
            $channels[] = new PrivateChannel('user.' . $listener->id);
        }
        return $channels;
    }

    private function getListeners(): array
    {
        switch ($this->user->job)
        {
            case User::SUPPLIER_JOB_NUMBER:
                return Permission::getUsersCan('create', new Supplier);
            case User::HIERD_SELLER_JOB_NUMBER:
                return Permission::getUsersCan('create', new Seller);
            case User::FREELANCER_SELLER_JOB_NUMBER:
                return Permission::getUsersCan('create', new Seller);
            case User::CUSTOMER_JOB_NUMBER:
                return Permission::getUsersCan('create', new Customer);
            case User::ONLINE_CLIENT_JOB_NUMBER:
                return Permission::getUsersCan('create', new Customer);
            default:
                return [];
        }
    }

    public function __get($prop)
    {
        return $this->$prop;
    }
}
