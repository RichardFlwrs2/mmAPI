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
Route::get('users/{id}/stats', 'User\UserController@stats');
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);
Route::resource('users.teams', 'User\UserTeamController', ['only' => ['index']]);
Route::resource('users.orders', 'User\UserOrderController', ['only' => ['index']]);

// -------------------------------------------------------------------------------
// * - Teams
// -------------------------------------------------------------------------------
Route::get('teams/{id}/stats', 'Team\TeamController@stats');
Route::resource('teams', 'Team\TeamController', ['except' => ['create', 'edit']]);
Route::resource('teams.users', 'Team\TeamUserController', ['only' => ['index']]);

// -------------------------------------------------------------------------------
// * - Clients
// -------------------------------------------------------------------------------
Route::resource('clients', 'Client\ClientController', ['except' => ['create', 'edit']]);
Route::resource('clients.contacts', 'Client\ClientContactController', ['only' => ['index']]);

// -------------------------------------------------------------------------------
// * - Contacts
// -------------------------------------------------------------------------------
Route::resource('contacts', 'Contact\ContactController', ['only' => ['update', 'store']]);

// -------------------------------------------------------------------------------
// * - Orders
// -------------------------------------------------------------------------------
Route::post('orders/{id}/petition', 'Order\OrderController@petition');
Route::post('orders/{id_order}/saveDataRecord/{id_record}', 'Order\OrderRecordController@saveDataRecord');
Route::resource('orders', 'Order\OrderController', ['except' => ['create', 'edit']]);
Route::resource('orders.records', 'Order\OrderRecordController', ['only' => ['index', 'show']]);
Route::resource('orders.status', 'Order\OrderStatusController', ['only' => ['update']]);

// -------------------------------------------------------------------------------
// * - Records
// -------------------------------------------------------------------------------
Route::resource('records', 'Record\RecordController', ['only' => ['show', 'store']]);

// -------------------------------------------------------------------------------
// * - Products
// -------------------------------------------------------------------------------
Route::resource('products', 'Product\ProductController', ['except' => ['create', 'edit']]);
Route::resource('products.files', 'Product\ProductFileController', ['only' => ['store', 'destroy', 'index']]);

// -------------------------------------------------------------------------------
// * - Files
// -------------------------------------------------------------------------------
Route::get('files/{id}/{type}', 'File\FileController@getFile');
Route::resource('files', 'File\FileController', ['only' => ['update', 'store']]);
