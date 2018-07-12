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

Route::get('/events/latest', 'Api\EventController@latest')->name('events.latest');
Route::resource('/events', 'Api\EventController')->only('show');
Route::resource('/venue', 'Api\VenueController')->only('show');
