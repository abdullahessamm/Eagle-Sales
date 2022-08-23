<?php

use App\Models\Customer;
use App\Models\Seller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->userData->id === (int) $id;
});

// create presence channels for online users
Broadcast::channel('online', function ($user) {
    return $user->userData->only(['id', 'f_name', 'l_name', 'avatar_uri']);
});
