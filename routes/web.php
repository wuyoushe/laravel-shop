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

Route::get('/', 'PagesController@root')->name('root');

Auth::routes(['verify' => true]);

Route::group(['middle' => ['auth', 'verified']], function (){
    Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
});

