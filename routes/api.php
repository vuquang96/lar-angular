<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
Route::post('authenticate', 'AuthenticateController@authenticate');

//Route::post('auth/register', 'UserController@register');
//Route::post('auth/login', 'UserController@login');
Route::post('auth/login', 'AuthenticateController@authenticate');
Route::group(['middleware' => 'verifyJWTToken'], function () {
	Route::group(['prefix' => 'administrator'], function(){
		Route::get('user', 'AuthenticateController@getAuthUser');
		Route::get('users', 'AuthenticateController@get_grid');
		Route::get('user/{id}', 'AuthenticateController@get_detail');
		Route::put('user/{id}', 'AuthenticateController@update');
		Route::delete('user/{id}', 'AuthenticateController@delete');
	});  
});