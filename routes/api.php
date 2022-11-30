<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'Auth\AuthController@register')->name('register');
Route::post('login', 'Auth\AuthController@login')->name('login');

Route::middleware('auth:api')->group(function () {
    Route::get('logout', 'Auth\AuthController@logout');
    Route::get('user','UserController@index');

    Route::post('user', 'UserController@store');
    Route::middleware('role:1')->group(function () {
        Route::put('approve-request/{id}', 'UserController@approveRequest');
        Route::delete('delete-user/{id}', 'UserController@destroy');
    });
});


