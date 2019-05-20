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
Route::post('/login', 'AuthController@login');
Route::post('/refresh', 'AuthController@refresh');
Route::post('/logout', 'AuthController@logout');

// -------------------------------------------------------------------------------
// * - Users
// -------------------------------------------------------------------------------
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']])->middleware('jwt');
Route::resource('users.teams', 'User\UserTeamController', ['only' => ['index']])->middleware('jwt');

// -------------------------------------------------------------------------------
// * - Teams
// -------------------------------------------------------------------------------
Route::resource('teams', 'Team\TeamController', ['except' => ['create', 'edit']])->middleware('jwt');

// -------------------------------------------------------------------------------
// * - Clients
// -------------------------------------------------------------------------------
Route::resource('clients', 'Client\ClientController', ['except' => ['create', 'edit']])->middleware('jwt');

// -------------------------------------------------------------------------------
// * - Orders
// -------------------------------------------------------------------------------
Route::resource('orders', 'Order\OrderController', ['except' => ['create', 'edit']])->middleware('jwt');
Route::resource('orders.records', 'Order\OrderRecordController', ['only' => ['index']])->middleware('jwt');

// -------------------------------------------------------------------------------
// * - Products
// -------------------------------------------------------------------------------
Route::resource('products', 'Product\ProductController', ['except' => ['create', 'edit']])->middleware('jwt');
