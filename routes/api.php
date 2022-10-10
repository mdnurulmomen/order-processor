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

				// Route::post('send-mobile-otp', 'LoginController@sendMobileOTP')->name('otp');
				Route::post('login', 'LoginController@login')->name('login');
				// Route::post('register', 'RegisterController@userRegistration')->name('register');
				
				Route::get('users/{user}', 'ProfileController@showUserDetail')->name('users.show');
				Route::put('user-settings/{user}', 'ProfileController@updateUserSetting')->name('user-settings.update');
				Route::put('user-profiles/{user}', 'ProfileController@updateUserProfile')->name('user-profiles.update');
				
				Route::post('addresses', 'ProfileController@addUserAddress')->name('user-addresses.create');
				Route::put('users/{user}/addresses/{address}', 'ProfileController@updateUserAddress')->name('user-addresses.update');
				
				// Route::post('user-password', 'ProfileController@updateUserPassword')->name('user-passwords.update');
				
				Route::get('user-favourites/{customer_address_id}/{per_page?}', 'CustomerController@getUserFavourites')->name('user-favourites.index');
				Route::post('user-favourites', 'CustomerController@addUserFavourite')->name('user-favourites.create');
				Route::delete('user-favourites/{customer_favourite_id}/{per_page?}', 'CustomerController@removeUserFavourite')->name('user-favourites.destroy');
				
				Route::post('/merchants', 'MerchantController@getMerchants')->name('merchants.index');
				Route::get('/merchants/{merchant}', 'MerchantController@showMerchantDetails')->name('merchants.show');
				// Route::post('/merchant-merchant-products', 'MerchantController@getMerchantMerchantProducts')->name('merchant-merchant-products.show');

				Route::post('/sponsor-merchants', 'MerchantController@getSponsorMerchants')->name('sponsor-merchants.index');
				Route::post('/promotional-merchant-products', 'MerchantController@getPromotionalMerchantProducts')->name('promotional-merchant-products.index');

				Route::get('/orders/{order}', 'OrderController@getOrderDetails')->name('orders.show');
				Route::get('/user-orders/{user}/{per_page?}', 'CustomerController@getUserOrders')->name('user-orders.index');
				Route::get('/user-active-orders/{user}/{per_page?}', 'CustomerController@getUserActiveOrders')->name('user-active-orders.index');
				Route::get('/user-reservations/{user}/{per_page?}', 'CustomerController@getUserReservations')->name('user-reservations.index');
				
				Route::get('/order-merchants/{order}', 'CustomerController@showOrderMerchants')->name('order-merchants.show');
				Route::post('/orders', 'OrderController@makeNewOrder')->name('orders.create');

				Route::get('/merchant-reviews/{merchant}/{per_page?}', 'MerchantController@getMerchantReviews')->name('merchant-reviews.show');
				Route::post('/merchant-reviews', 'MerchantController@addMerchantReview')->name('merchant-reviews.create');
				
				Route::post('reservations', 'OrderController@makeNewReservation')->name('reservations.create');
				Route::post('reservation-confirmations', 'OrderController@confirmReservation')->name('reservation-confirmations.create');

				Route::get('/my-regulars/{user}/{per_page?}', 'CustomerController@getMyRegularProducts')->name('my-regulars.index');
				Route::post('/my-regulars', 'CustomerController@setMyRegularProducts')->name('my-regulars.create');
				Route::put('/my-regulars/{regular}/{per_page?}', 'CustomerController@updateMyRegularProducts')->name('my-regulars.update');
				Route::delete('/my-regulars/{regular}/{per_page?}', 'CustomerController@deleteMyRegularProducts')->name('my-regulars.delete');

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
