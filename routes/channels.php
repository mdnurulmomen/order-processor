<?php

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
/*
Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
*/

// General channel for all admin with Authentication 
Broadcast::channel('notifyAdmin', function ($admin) {
    return true;
}, ['guards' => 'admin']);

// separate channel for each owner with Authentication
Broadcast::channel('notifyOwner.{ownerId}', function ($owner, $ownerId) {
    return (int) $owner->id == (int) $ownerId;
}, ['guards' => ['admin', 'owner']]);

// separate channel for each merchant with Authentication
Broadcast::channel('notifyMerchant.{merchantId}', function ($merchant, $merchantId) {
    return (int) $merchant->id == (int) $merchantId;
}, ['guards' => ['admin', 'merchant']]);

// for now 'admin' guard is listed along with 'api' guard
Broadcast::channel('notifyRider.{riderId}', function ($rider, $riderId) {
    return (int) $rider->id == (int) $riderId;
}, ['guards' => ['admin', 'api']]);

// for now 'admin' guard is listed along with 'merchant' guard
Broadcast::channel('notifyMerchantAgents.{merchantId}', function ($agent) {
    return true;
}, ['guards' => ['admin', 'api']]);

