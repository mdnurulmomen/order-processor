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
		Route::name('api.v1.')->group(function () {
			Route::prefix('v1')->group(function () {

				Route::get('/general-info', 'SettingController@getGeneralInfo')->name('general-info');

				Route::post('login', 'LoginController@userLogin')->name('login');
				Route::post('register', 'RegisterController@userRegistration')->name('register');
				
				Route::get('users/{user}', 'ProfileController@showUserDetail')->name('users.show');
				Route::put('user-settings/{user}', 'ProfileController@updateUserSetting')->name('user-settings.update');
				Route::put('user-profiles/{user}', 'ProfileController@updateUserProfile')->name('user-profiles.update');
				
				Route::post('user-addresses', 'ProfileController@addUserAddress')->name('user-addresses.create');
				Route::put('users/{user}/addresses/{address}', 'ProfileController@updateUserAddress')->name('user-addresses.update');
				
				Route::post('user-password', 'ProfileController@updateUserPassword')->name('user-passwords.update');
				
				Route::get('user-favourites/{customer_address_id}/{perPage?}', 'CustomerController@getUserFavourites')->name('user-favourites.index');
				Route::post('user-favourites', 'CustomerController@addUserFavourite')->name('user-favourites.create');
				Route::delete('user-favourites/{customer_favourite_id}/{perPage?}', 'CustomerController@removeUserFavourite')->name('user-favourites.destroy');
				
				Route::post('/restaurants', 'RestaurantController@getRestaurants')->name('restaurants.index');
				Route::get('/restaurants/{restaurant}', 'RestaurantController@showRestaurantDetails')->name('restaurants.show');
				// Route::post('/restaurant-menu-items', 'RestaurantController@getRestaurantMenuItems')->name('restaurant-menu-items.show');

				Route::get('/user-orders/{user}/{perPage?}', 'OrderController@getUserOrders')->name('user-orders.index');
				Route::post('/orders', 'OrderController@createNewOrder')->name('orders.create');

				Route::get('/restaurant-reviews/{restaurant}/{perPage?}', 'RestaurantController@getRestaurantReview')->name('restaurant-reviews.show');
				Route::post('/restaurant-reviews', 'RestaurantController@addRestaurantReview')->name('restaurant-reviews.create');
				
				Route::post('reservations', 'OrderController@createNewReservation')->name('reservations.create');
				Route::post('reservation-confirmations', 'OrderController@confirmReservation')->name('reservation-confirmations.create');

			});

			Route::any('{any}', function(){
			    return response()->json([
			        'status'    => false,
			        'message'   => 'Route Not Found.',
			    ], 500);
			})->where('any', '.*');
		});
	});
// });
