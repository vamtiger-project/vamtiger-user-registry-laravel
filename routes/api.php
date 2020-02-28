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

Route::post('add-new-user', 'UserRegistryController@addNewuser');
Route::patch('update-user/{id}', 'UserRegistryController@updateUser');
Route::delete('delete-user/{id}', 'UserRegistryController@deleteUser');
Route::get('get-user/{id}', 'UserRegistryController@getUser');
Route::get('get-users', 'UserRegistryController@getUsers');