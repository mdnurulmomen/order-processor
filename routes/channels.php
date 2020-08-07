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

Broadcast::channel('notifyAdmin', function ($user) {
    return true;
}, ['guards' => 'admin']);

Broadcast::channel('notifyRestaurant.{restaurantId}', function ($restaurant, $restaurantId) {
    return (int) $restaurant->id == $restaurantId;
}, ['guards' => 'restaurant']);

/*
Broadcast::channel('notifyRestaurant', function ($restaurant) {
	return true;
}, ['guards' => ['admin', 'restaurant']]);
*/


// for now 'admin' guard is listed along with 'api' guard
Broadcast::channel('notifyRider.{riderId}', function ($rider, $riderId) {
    return $rider->id == $riderId;
}, ['guards' => ['admin', 'api']]);


/*
Broadcast::channel('notifyRider', function ($rider) {
    return true;
}, ['guards' => 'admin']);
*/
