<?php

use Illuminate\Http\Request;
use  Illuminate\Http\Response;
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
Route::post('/inbox','ATsmsController@inbox');
Route::post('/blacklist','ATsmsController@blacklist');
Route::fallback('ATsmsController@notFound');
