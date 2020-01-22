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

Auth::routes();

Route::view('/', 'layouts.website')->name('website');

Route::get('/home', 'Auth\HomeController@index')->name('customer.home');

Route::fallback(function () {
    Route::view('/', 'layouts.website');
});


