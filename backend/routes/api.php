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

Route::post('registers', [
    'uses' => 'UsersController@postUser'
]);

//user routes
//Route::post('/user/register', [
//    'uses' => 'UsersController@postUser'
//]);
Route::post('/user/login', [
    'uses' => 'UsersController@login'
]);

Route::group(['prefix' => 'user',  'middleware' => 'auth.jwt'], function () {
    Route::get('/', [
        'uses' => 'UsersController@getAuthUser'
    ]);

    Route::post('register', [
        'uses' => 'UsersController@postUser'
    ]);

    Route::put('{id}', [
        'uses' => 'UsersController@putUser'
    ]);

    Route::delete('{id}', [
        'uses' => 'UsersController@deleteUser'
    ]);

    Route::put('{id}/apto', [
        'uses' => 'UsersController@removeUserFromApto'
    ]);

    Route::post('get-notifications',[
        'uses' => 'UsersController@getNotifications'
    ]);

    Route::post('mark-read',[
        'uses' => 'UsersController@markAsRead'
    ]);

    Route::get('stats', [
        'uses' => 'UsersController@stats'
    ]);
});


Route::group(['prefix' => 'users',  'middleware' => 'auth.jwt'], function () {
    Route::post('register', [
        'uses' => 'UsersController@postUsers'
    ]);

    Route::get('/', [
        'uses' => 'UsersController@getUsers'
    ]);
});



//apto routes
Route::group(['prefix' => 'apto',  'middleware' => 'auth.jwt'], function () {

    Route::post('/register', [
        'uses' => 'ApartamentController@postApto'
    ]);

    Route::post('/bulk-register', [
        'uses' => 'ApartamentController@postAptos'
    ]);

    Route::get('/all', [
        'uses' => 'ApartamentController@getAptos'
    ]);

    Route::get('/', [
        'uses' => 'ApartamentController@getVacancyAptos'
    ]);

    Route::get('/{number}', [
        'uses' => 'ApartamentController@getApto'
    ]);

    Route::put('/{number}', [
        'uses' => 'ApartamentController@putApto'
    ]);

    Route::delete('/{number}', [
        'uses' => 'ApartamentController@deleteApto'
    ]);


    /*Rota referente a notificação de solicitação de troca de apartamento*/
    Route::post('/change-notify',[
        'uses' => 'ApartamentController@changeApto'

    ]);
});

//apto routes

//course routes

Route::post('/course/register', [
    'uses' => 'CourseController@postCourse'
]);

Route::post('/courses/register',[
    'uses' => 'CourseController@postCourses'
]);

Route::get('/courses', [
    'uses' => 'CourseController@getCourses'
]);

Route::put('/course/{id}', [
    'uses' => 'CourseController@putCourse'
]);

Route::delete('course/{id}', [
    'uses' => 'CourseController@deleteCourse'
]);


//course routes

//WAIT LIST

Route::group(['prefix' => 'waitList',  'middleware' => 'auth.jwt'], function () {

    Route::post('/register', [
        'uses' => 'WaitListController@postWaitList'
    ]);
});
