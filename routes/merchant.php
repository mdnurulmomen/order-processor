<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::name('merchant.')->group(function () {
    
    Route::namespace('Auth')->group(function () {
	
		Route::get('/', 'LoginController@showMerchantLoginForm')->name('login');
		Route::post('/', 'LoginController@merchantLogin');
	    
	    // For all other routes coming from vue
    	Route::get('/{any}/{id?}', 'HomeController@showMerchantHome')->where('id', '[0-9]+');	

		Route::group(['middleware' => ['auth:merchant']], function () {
		    
		    Route::post('/logout', 'LoginController@merchantLogout')->name('logout');

		});
	});

	Route::namespace('Web')->group(function () {

		Route::group(['middleware' => ['auth:merchant']], function () {
		    
		    // Profile
		    Route::get('/api/profile', 'ProfileController@showMerchantProfile');
		    Route::post('/profile', 'ProfileController@updateMerchantProfile');

		    Route::post('/password', 'PasswordController@updateMerchantPassword');

		    // Merchnat-Meals
		    Route::get('/merchant-meals/{merchant}/{perPage}', 'MerchantController@showMerchantAllMeals');
		    Route::get('/merchant-meals/{merchant}/{search}/{perPage}', 'MerchantController@searchMerchantAllMeals');

		    // My-Cuisines
		    Route::get('/merchant-cuisines/{merchant}/{perPage}', 'MerchantController@showMerchantAllCuisines');
		    Route::get('/merchant-cuisines/{merchant}/{search}/{perPage}', 'MerchantController@searchMerchantAllCuisines');

		    // My-Products
		    Route::get('/api/merchant-products/{merchant}/{perPage?}', 'MerchantController@showMerchantAllProducts');
		    Route::put('/merchant-products/{merchant}/{perPage}', 'MerchantController@updateMerchantProductStock');
		    Route::get('/merchant-products/{merchant}/{search}/{perPage}', 'MerchantController@searchMerchantAllProducts');

		    // My-Product-Categores
		    Route::get('/api/merchant-product-categories/{merchant}/{perPage?}', 'MerchantController@showMerchantAllProductCategories');
		    Route::get('/merchant-product-categories/{merchant}/{search}/{perPage}', 'MerchantController@searchMerchantAllProductCategories');
		    // Route::put('/merchant-menu-categories/{merchant}/{perPage}', 'MerchantController@updateMerchantMenuCategoryStock');

		    // My-Kitchen
		    Route::get('/api/my-kitchen/{merchant}', 'MerchantController@showMerchantKitchen');
		    // Route::put('/my-kitchen/{merchant}', 'MerchantController@updateMerchantKitchen');
		    // Route::put('/my-kitchen/{merchant}', 'MerchantController@updateMerchantMenuItemStock');

		    // Cancellation-Reasons
		    Route::get('/api/cancellation-reasons/{perPage?}', 'CancellationController@showAllReasons');

		    // Orders
		    Route::get('/orders/{merchant}/{perPage?}', 'OrderController@showMerchantAllOrders');
		    Route::post('/orders/{order}/{perPage}', 'OrderController@confirmMerchantOrder');
		    Route::put('/orders/{order}/{perPage}', 'OrderController@cancelMerchantOrder');
		    // Route::get('/api/orders/search/{search}/{perPage}', 'OrderController@searchAllOrders');

		    // Merchant-Agents
		    Route::get('/merchant-agents/{merchant}/{perPage}', 'MerchantController@showMerchantAllAgents');
		    Route::get('/merchant-agents/{merchant}/{search}/{perPage}', 'MerchantController@searchMerchantAllAgents');

		    // Route::get('/api/settings', 'SettingController@showAllSettings');
		    // Route::post('/settings', 'SettingController@updatePaymentSettings');

		});
	});
});

