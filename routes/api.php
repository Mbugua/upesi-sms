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
Route::post('/sms','ATsmsController@sms')->name('sms');
Route::get('/messages','ATsmsController@messages')->name('messages');
Route::get('/notify','ATsmsController@notify')->name('notify');
Route::get('/incoming','ATsmsController@incoming')->name('inbox');

Route::fallback(function(){
    return response()->json(['success'=>true,
    'data'=>['success'=>false,'error'=>404,'message'=>'Not Found']],404);
})->name('fallback');
