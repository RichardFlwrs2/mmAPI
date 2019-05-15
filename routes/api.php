<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// -------------------------------------------------------------------------------
// * - Users
// -------------------------------------------------------------------------------
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);

// -------------------------------------------------------------------------------
// * - Orders
// -------------------------------------------------------------------------------
Route::resource('orders', 'Order\OrderController', ['except' => ['create', 'edit']]);

// -------------------------------------------------------------------------------
// * - Products
// -------------------------------------------------------------------------------
Route::resource('products', 'Product\ProductController', ['except' => ['create', 'edit']]);
