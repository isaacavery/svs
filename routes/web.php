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

Route::get('/', 'HomeController@index')->name('home');
Route::resource('sheets','SheetController');
Route::get('circulators/queue', 'CirculatorController@queue')->middleware('auth');
Route::post('circulators/search', 'CirculatorController@queue');
Route::post('circulators/add', 'CirculatorController@add');