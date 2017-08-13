<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/', 'HomeController@index')->name('home')->middleware('auth');
Route::get('sheets/test', 'SheetController@queue')->middleware('auth');
Route::resource('sheets','SheetController');
Route::get('circulators/queue', 'CirculatorController@queue')->middleware('auth');
Route::post('circulators/search', 'CirculatorController@search')->middleware('auth');
Route::post('circulators/add', 'CirculatorController@add')->middleware('auth');