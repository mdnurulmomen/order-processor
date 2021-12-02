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
				
				Route::get('user-favourites/{customer_address_id}/{per_page?}', 'CustomerController@getUserFavourites')->name('user-favourites.index');
				Route::post('user-favourites', 'CustomerController@addUserFavourite')->name('user-favourites.create');
				Route::delete('user-favourites/{customer_favourite_id}/{per_page?}', 'CustomerController@removeUserFavourite')->name('user-favourites.destroy');
				
				Route::post('/restaurants', 'RestaurantController@getRestaurants')->name('restaurants.index');
				Route::get('/restaurants/{restaurant}', 'RestaurantController@showRestaurantDetails')->name('restaurants.show');
				// Route::post('/restaurant-menu-items', 'RestaurantController@getRestaurantMenuItems')->name('restaurant-menu-items.show');

				Route::post('/sponsor-restaurants', 'RestaurantController@getSponsorRestaurants')->name('sponsor-restaurants.index');
				Route::post('/promotional-menu-items', 'RestaurantController@getPromotionalMenuItems')->name('promotional-menu-items.index');

				Route::get('/orders/{order}', 'OrderController@getOrderDetails')->name('orders.show');
				Route::get('/user-orders/{user}/{per_page?}', 'CustomerController@getUserOrders')->name('user-orders.index');
				Route::get('/user-reservations/{user}/{per_page?}', 'CustomerController@getUserReservations')->name('user-reservations.index');
				
				Route::get('/order-restaurants/{order}', 'CustomerController@showOrderRestaurants')->name('order-restaurants.show');
				Route::post('/orders', 'OrderController@createNewOrder')->name('orders.create');

				Route::get('/restaurant-reviews/{restaurant}/{per_page?}', 'RestaurantController@getRestaurantReview')->name('restaurant-reviews.show');
				Route::post('/restaurant-reviews', 'RestaurantController@addRestaurantReview')->name('restaurant-reviews.create');
				
				Route::post('reservations', 'OrderController@createNewReservation')->name('reservations.create');
				Route::post('reservation-confirmations', 'OrderController@confirmReservation')->name('reservation-confirmations.create');

				Route::get('/my-regulars/{user}/{per_page?}', 'CustomerController@getMyRegularItems')->name('my-regulars.index');
				Route::post('/my-regulars', 'CustomerController@createMyRegularItems')->name('my-regulars.create');
				Route::put('/my-regulars/{regular}/{per_page?}', 'CustomerController@updateMyRegularItems')->name('my-regulars.update');
				Route::delete('/my-regulars/{regular}/{per_page?}', 'CustomerController@deleteMyRegularItems')->name('my-regulars.delete');

				Route::post('/logout', 'CustomerController@logout')->name('logout');

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
