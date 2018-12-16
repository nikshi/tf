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

Route::post('/contracts/create', 'FarmerController@createContract');

Route::post('/reports/rental', 'FarmerController@reportRental');
Route::post('/reports/own', 'FarmerController@reportOwn');
