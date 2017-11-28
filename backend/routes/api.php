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

#Route:group(['prefix' =>'user-admin','middleware' => ['auth.jwt', 'is-admin']], function(){

#});



#This routes are only accessible for admins

Route::group(['prefix' => 'user',  'middleware' => ['auth.jwt', 'is-admin']], function () {
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

    Route::put('{id}/apto/{apto}', [
        'uses' => 'UsersController@addUserToApto'
    ]);

    Route::put('apto/change', [
        'uses' => 'UsersController@changeUserApto'
    ]);

    Route::post('create-notification',[
        'uses' => 'UsersController@createAlert'
    ]);

    Route::get('is-admin',[
        'uses' => 'UsersController@isAdmin'
    ]);
});


Route::group(['prefix' => 'user',  'middleware' => 'auth.jwt'], function () {
    Route::get('get-notifications',[
        'uses' => 'UsersController@getNotifications'
    ]);

    Route::post('mark-read',[
        'uses' => 'UsersController@markAsRead'
    ]);

    Route::get('stats', [
        'uses' => 'UsersController@stats'
    ]);
});

#This routes are only accessible for admins
Route::group(['prefix' => 'users',  'middleware' => ['auth.jwt', 'is-admin']], function () {
    Route::post('register', [
        'uses' => 'UsersController@postUsers'
    ]);

    Route::get('/', [
        'uses' => 'UsersController@getUsers'
    ]);

    Route::get('/noapto', [
        'uses' => 'UsersController@getUsersWithoutApto'
    ]);

    Route::get('/from/{id_apto}', [
       'uses' => 'UsersController@getUsersFromApto'
    ]);

});

#This routes are only accessible for admins
Route::group(['prefix' => 'laundry', 'middleware' => ['auth.jwt', 'is-admin']], function(){

    /*Machines*/
    Route::post('new-machine',[
        'uses' => 'LaundryMachineController@postLaundryMachine'
    ]);

    Route::get('get-machines',[
       'uses' => 'LaundryMachineController@getLaundryMachines'
    ]);

    Route::get('get-machine/{id}',[
        'uses' => 'LaundryMachineController@getLaundryMachine'
    ]);

    Route::put('update-machine/{id}',[
        'uses' => 'LaundryMachineController@putLaundryMachine'
    ]);


    /*Working time*/
    Route::post('new-working-time',[
        'uses' => 'StandardWorkingTimeController@postStandardWorkingTime'
    ]);

    /*No working Time*/
    Route::post('no-working-time',[
       'uses' => 'NoWorkingTimeController@postNoWorkingTime'
    ]);
});


Route::group(['prefix' => 'laundry', 'middleware' => 'auth.jwt'], function() {
    /*New booking*/
    Route::post('new-booking',[
        'uses' => 'LaundryMachineBookingController@postLMBooking'
    ]);

    Route::get('get-working-time',[
        'uses' => 'StandardWorkingTimeController@getWorkingTime'
    ]);
});

//apto routes
Route::group(['prefix' => 'apto',  'middleware' => ['auth.jwt', 'is-admin']], function () {

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

    Route::get('/with-students', [
        'uses' => 'ApartamentController@getAptosWithStudents'
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

Route::group(['prefix' => 'apto',  'middleware' => ['auth.jwt'. 'is-admin']], function () {
    Route::post('/course/register', [
        'uses' => 'CourseController@postCourse'
    ]);

    Route::post('/courses/register', [
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
});

//course routes

