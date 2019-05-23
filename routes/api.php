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
Route::get('users/{id}/stats', 'User\UserController@stats')->middleware('jwt');
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']])->middleware('jwt');
Route::resource('users.teams', 'User\UserTeamController', ['only' => ['index']])->middleware('jwt');
Route::resource('users.orders', 'User\UserOrderController', ['only' => ['index']])->middleware('jwt');

// -------------------------------------------------------------------------------
// * - Teams
// -------------------------------------------------------------------------------
Route::get('teams/{id}/stats', 'Team\TeamController@stats')->middleware('jwt');
Route::resource('teams', 'Team\TeamController', ['except' => ['create', 'edit']])->middleware('jwt');
Route::resource('teams.users', 'Team\TeamUserController', ['only' => ['index']])->middleware('jwt');

// -------------------------------------------------------------------------------
// * - Clients
// -------------------------------------------------------------------------------
Route::resource('clients', 'Client\ClientController', ['except' => ['create', 'edit']])->middleware('jwt');
Route::resource('clients.contacts', 'Client\ClientContactController', ['only' => ['index']])->middleware('jwt');

// -------------------------------------------------------------------------------
// * - Contacts
// -------------------------------------------------------------------------------
Route::resource('contacts', 'Contact\ContactController', ['only' => ['update', 'store']])->middleware('jwt');

// -------------------------------------------------------------------------------
// * - Orders
// -------------------------------------------------------------------------------
Route::resource('orders', 'Order\OrderController', ['except' => ['create', 'edit']])->middleware('jwt');
Route::resource('orders.records', 'Order\OrderRecordController', ['only' => ['index', 'show']])->middleware('jwt');
Route::resource('orders.status', 'Order\OrderStatusController', ['only' => ['update']])->middleware('jwt');

// -------------------------------------------------------------------------------
// * - Records
// -------------------------------------------------------------------------------
Route::resource('records', 'Record\RecordController', ['only' => ['show', 'store']])->middleware('jwt');

// -------------------------------------------------------------------------------
// * - Products
// -------------------------------------------------------------------------------
Route::resource('products', 'Product\ProductController', ['except' => ['create', 'edit']])->middleware('jwt');
