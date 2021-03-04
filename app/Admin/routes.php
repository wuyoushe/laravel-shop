<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->resource('products', ProductsController::class);

    $router->get('users', 'UsersController@index');

    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');

    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');

    $router->post('orders/{order}/ship', 'OrderController@ship')->name('admin.orders.ship');

    $router->resource('coupon-codes', CouponCodesController::class);

});


























