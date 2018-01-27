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

Route::get('curl','DataController@Curl');
Route::get('getData',['as' => 'getData','uses' => 'DataController@getData']);
Route::get('editData/{id}',['as' => 'editData','uses' => 'DataController@formData']);
Route::post('updateData',['as' => 'updateData','uses' => 'DataController@updateData']);
Route::post('searchData',['as' => 'searchData','uses' => 'DataController@searchData']);
Route::get('datatable/getdata',['as' => 'datatable.getdata','uses' => 'DataController@anyData']);