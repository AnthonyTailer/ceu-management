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

Route::get('/test', function () {
   return response([1,2,3,4,34,5], 200);
});

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => '/user'], function () {

    Route::post('/login', [
        'uses' => 'UsersController@login'
    ]);

    Route::post('/register', [
        'uses' => 'UsersController@register'
    ]);

    Route::group(['middleware' => 'jwt-auth'], function () {

        Route::get('/user', [
            'uses' => 'UserController@getAuthUser'
        ]);
    });

});

