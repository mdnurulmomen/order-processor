<?php

// use Illuminate\Http\Request;

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

			Route::post('login', 'LoginController@userLogin');
			Route::post('register', 'RegisterController@userRegistration');
			
			Route::get('users/{user}', 'ProfileController@showUserDetail');
			Route::put('user-settings/{user}', 'ProfileController@updateUserSetting');
			Route::put('user-profiles/{user}', 'ProfileController@updateUserProfile');
			
			Route::post('user-addresses', 'ProfileController@addUserAddress');
			Route::put('user-addresses/{user}/{address}', 'ProfileController@updateUserAddress');
			
			Route::post('user-password', 'ProfileController@updateUserPassword');
			
			Route::get('user-favourites/{customer_address_id}', 'CustomerController@getUserFavourites');
			Route::post('user-favourites', 'CustomerController@addUserFavourite');
			Route::delete('user-favourites/{customer_favourite_id}', 'CustomerController@removeUserFavourite');
			
			Route::post('/restaurants', 'RestaurantController@getRestaurants');
			Route::post('/restaurant-menu-items', 'RestaurantController@getRestaurantMenuItems');

			Route::get('/user-orders/{user}/{perPage?}', 'OrderController@getUserOrders');
			Route::post('/orders', 'OrderController@createNewOrder');

			Route::get('/restaurant-reviews/{restaurant}/{perPage?}', 'RestaurantController@getRestaurantReview');
			Route::post('/restaurant-reviews', 'RestaurantController@addRestaurantReview');
			
			Route::post('reservations', 'OrderController@createNewReservation');
			Route::post('reservation-confirmations', 'OrderController@confirmReservation');

			Route::any('{any}', function(){
			    return response()->json([
			        'status'    => false,
			        'message'   => 'Route Not Found.',
			    ], 500);
			})->where('any', '.*');

		});
	});
// });
