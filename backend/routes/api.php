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

    /**
     * Races
     */
    Route::get('/all-races', 'RacesController@index');
    Route::get('/active-races', 'RacesController@actives');
    Route::get('/races/{id}', 'RacesController@show');
    Route::post('/races', 'RacesController@create');
    Route::put('/races/{id}', 'RacesController@update');
    Route::delete('/races/{id}', 'RacesController@destroy');

    Route::get('/progress', 'RacesController@progress');