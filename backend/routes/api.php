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


    /**
     * Horses
     */
    Route::get('/horses', 'HorseController@index');
    Route::get('/horses/{id}', 'HorseController@show');
    Route::post('/horses', 'HorseController@create');
    Route::put('/horses/{id}', 'HorseController@update');
    Route::delete('/horses/{id}', 'HorseController@destroy');