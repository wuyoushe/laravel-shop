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

Auth::routes(['verify' => true]);

Route::group(['middle' => ['auth', 'verified']], function (){
    Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
});

Route::group(['middle' => ['auth']], function () {
    Route::get('user_addresses', 'UserAddressController@index')->name('user_addresses.index');

    Route::get('user_addresses/create', 'UserAddressController@create')->name('user_addresses.create');

    Route::post('products/{product}/favorite','ProductsController@favor')->name('products.favor');
    Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');

    //加入购物车
    Route::post('cart', 'CartController@add')->name('cart.add');
    Route::get('cart', 'CartController@index')->name('cart.index');
    Route::delete('cart/{sku}', 'CartController@remove')->name('cart.remove');

    Route::post('orders', 'OrdersController@store')->name('orders.store');

    Route::get('orders', 'OrdersController@index')->name('orders.index');

    Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');

    Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');

    Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');
});

Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');

Route::post('payment/wechat/notify', 'PaymentController@wechatNotify')->name('payment.wechat.notify');

Route::get('products/{product}', 'ProductsController@show')->name('products.show');


