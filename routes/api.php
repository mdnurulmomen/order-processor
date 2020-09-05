<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

// Route::group(['middleware' => ['auth:api']], function () {
	Route::namespace('Api')->group(function () {
		Route::name('api.')->group(function () {    

			Route::get('/general-info', 'SettingController@getGeneralInfo');
			
			Route::get('/restaurants/{expectedLatitude}/{expectedLongitude}', 'RestaurantController@getRestaurants');
			Route::get('/restaurant-menu-items/{expectedRestaurant}', 'RestaurantController@getRestaurantMenuItems');

			Route::post('/orders', 'OrderController@createNewOrder');
			
			Route::post('reservations', 'OrderController@createNewReservation');
			Route::post('reservation-confirmations', 'OrderController@confirmReservation');

		});
	});
// });
