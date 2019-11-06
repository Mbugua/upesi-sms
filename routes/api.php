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


Route::post('/outbox','ATsmsController@outbox');
Route::post('/messages','ATsmsController@messages');
Route::post('/notify','ATsmsController@notify');
Route::post('/incoming','ATsmsController@incoming');
Route::post('/blacklist','ATsmsController@blacklist');
Route::post('/subscription','ATsmsController@subscription');
Route::fallback('ATsmsController@notFound');
