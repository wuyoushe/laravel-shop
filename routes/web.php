<?php

use Illuminate\Support\Facades\Route;

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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::redirect('/', '/products')->name('root');
Route::get('products', 'ProductsController@index')->name('products.index');

Auth::routes();

Route::group(['middle' => ['auth']], function () {
    Route::get('user_addresses', 'UserAddressController@index')->name('user_addresses.index');

    Route::get('user_addresses/create', 'UserAddressController@create')->name('user_addresses.create');
});

Route::get('products/{product}', 'ProductsController@show')->name('products.show');


