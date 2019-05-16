<?php

// header('Access-Control-Allow-Origin:  *');
// header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

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
// * - Auth
// -------------------------------------------------------------------------------
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);

// -------------------------------------------------------------------------------
// * - Users
// -------------------------------------------------------------------------------
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);

// -------------------------------------------------------------------------------
// * - Clients
// -------------------------------------------------------------------------------
Route::resource('clients', 'Client\ClientController', ['except' => ['create', 'edit']]);

// -------------------------------------------------------------------------------
// * - Orders
// -------------------------------------------------------------------------------
Route::resource('orders', 'Order\OrderController', ['except' => ['create', 'edit']]);
Route::resource('orders.records', 'Order\OrderRecordController', ['only' => ['index']]);

// -------------------------------------------------------------------------------
// * - Products
// -------------------------------------------------------------------------------
Route::resource('products', 'Product\ProductController', ['except' => ['create', 'edit']]);
