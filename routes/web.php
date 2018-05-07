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
Route::get('api/searchSigned','ApiController@searchSigned');

Auth::routes();

Route::model('users', 'User');
Route::resource('users', 'UserController');
Route::get('users/{id}', 'UserController@view')->middleware('auth');
Route::post('users/update', 'UserController@update')->middleware('auth')->name('users.update');
Route::get('/', 'HomeController@index')->name('home')->middleware('auth');
Route::get('api/test/searchSigned', 'HomeController@searchSignedTest')->name('searchSignedTest')->middleware('auth');
Route::get('sheets/queue', 'SheetController@queue')->middleware('auth');
Route::resource('sheets','SheetController');
Route::get('sheets/checkCompletion/{id}', 'SheetController@checkCompletion')->middleware('auth');
Route::get('circulators/queue', 'CirculatorController@queue')->middleware('auth');
Route::post('circulators/search', 'CirculatorController@search')->middleware('auth');
Route::post('circulators/ajaxSelect', 'CirculatorController@ajaxSelect')->middleware('auth');
Route::post('circulators/ajaxRemoveCirculator', 'CirculatorController@ajaxRemoveCirculator')->middleware('auth');
Route::post('circulators/add', 'CirculatorController@add')->middleware('auth');
Route::get('circulators/checkCompletion/{id}', 'CirculatorController@checkCompletion')->middleware('auth');
Route::resource('signers', 'SignerController');
Route::post('signers/search', 'SignerController@search')->middleware('auth');
Route::get('reports/circulators', 'ReportsController@circulators')->middleware('auth');
Route::get('reports/signers', 'ReportsController@signers')->middleware('auth');
Route::get('reports/duplicates/download', 'ReportsController@duplicatesDownload')->middleware('auth');
Route::get('reports/duplicates', 'ReportsController@duplicates')->middleware('auth');
Route::get('signers/delete/{id}', 'SignerController@delete')->middleware('auth');
Route::get('signers/restore/{id}', 'SignerController@restore')->middleware('auth');