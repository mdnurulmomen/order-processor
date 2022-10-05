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

Route::name('owner.')->group(function () {
    
    Route::namespace('Auth')->group(function () {
	
		Route::get('/', 'LoginController@showOwnerLoginForm')->name('login');
		Route::post('/', 'LoginController@ownerLogin');
	    
	    // For all other routes coming from vue
    	Route::get('/{any}/{id?}', 'HomeController@showOwnerHome')->where('id', '[0-9]+');

		Route::group(['middleware' => ['auth:owner']], function () {
		    
		    Route::post('/logout', 'LoginController@ownerLogout')->name('logout');

		});
	});

	Route::namespace('Web')->group(function () {

		Route::group(['middleware' => ['auth:owner']], function () {
		    
		    // Profile
		    Route::get('/api/profile', 'ProfileController@showOwnerProfile');
		    Route::post('/profile', 'ProfileController@updateOwnerProfile');

		    Route::post('/password', 'PasswordController@updateOwnerPassword');

		    // Merchnat-Meals
		    Route::get('/merchant-meals/{merchant}/{perPage}', 'ProductController@showMerchantAllMeals');
		    Route::get('/merchant-meals/{merchant}/{search}/{perPage}', 'ProductController@searchMerchantAllMeals');

		    // My-Cuisines
		    Route::get('/merchant-cuisines/{merchant}/{perPage}', 'ProductController@showMerchantAllCuisines');
		    Route::get('/merchant-cuisines/{merchant}/{search}/{perPage}', 'ProductController@searchMerchantAllCuisines');

		    // My-Products
		    Route::get('/api/merchant-products/{merchant}/{perPage?}', 'ProductController@showMerchantAllProducts');
		    Route::put('/merchant-products/{merchant}/{perPage}', 'ProductController@updateMerchantProductStock');
		    Route::get('/merchant-products/{merchant}/{search}/{perPage}', 'ProductController@searchMerchantAllProducts');

		    // My-Product-Categores
		    Route::get('/api/merchant-product-categories/{merchant}/{perPage?}', 'ProductController@showMerchantAllProductCategories');
		    Route::get('/merchant-product-categories/{merchant}/{search}/{perPage}', 'ProductController@searchMerchantAllProductCategories');
		    // Route::put('/merchant-menu-categories/{merchant}/{perPage}', 'MerchantController@updateMerchantMenuCategoryStock');

		    // My-Kitchen
		    Route::get('/api/my-kitchen/{merchant}', 'MerchantController@showMerchantKitchen');
		    // Route::put('/my-kitchen/{merchant}', 'MerchantController@updateMerchantKitchen');
		    // Route::put('/my-kitchen/{merchant}', 'MerchantController@updateMerchantMenuItemStock');

		    // Cancelation-Reasons
		    Route::get('/api/cancelation-reasons/{perPage?}', 'CancelationController@showAllReasons');

		    // Orders
		    Route::get('/orders/{merchant}/{perPage?}', 'OrderController@showAllMerchantOrders');
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

