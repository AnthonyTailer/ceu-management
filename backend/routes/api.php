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

//user routes

Route::post('/user/register', [
    'uses' => 'UsersController@postUser'
]);

Route::get('/users', [
    'uses' => 'UsersController@getUsers'
]);

Route::put('/user/{id}', [
    'uses' => 'UsersController@putUser'
]);

Route::delete('/user/{id}', [
    'uses' => 'UserController@deleteUser'
]);

//user routes


//block routes

Route::post('/block/register', [
    'uses' => 'BlockController@postBlock'
]);

Route::get('/blocks', [
    'uses' => 'BlockController@getBlocks'
]);

Route::put('/block/{id}', [
    'uses' => 'BlockController@putBlocks'
]);

Route::delete('/block/{id}', [
    'uses' => 'BlockController@deleteBlock'
]);

//block routes

//apto routes

Route::post('/apto/register', [
    'uses' => 'ApartamentController@postApto'
]);

Route::get('/aptos', [
    'uses' => 'ApartamentController@getAptos'
]);

Route::put('/apto/{id}', [
    'uses' => 'ApartamentController@putAptos'
]);

Route::delete('/aptoblock/{id}', [
    'uses' => 'ApartamentController@deleteAptos'
]);

//apto routes

//course routes

Route::post('/course/register', [
    'uses' => 'CourseController@postCourse'
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

