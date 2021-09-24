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


Route::group(['middleware' => 'web', 'prefix' => 'contentmanager', 'namespace' => 'Modules\ContentManager\Http\Controllers'], function () {
    Route::get('/', 'ContentManagerController@index');
});


Route::group([
    'middleware' => ['web', 'auth'],
    'prefix' => 'admin', 'namespace' => 'Modules\ContentManager\Http\Controllers\Admin'
], function () {
    Route::get('content-manager/view', array('as' => 'content-manager.view', 'uses' => 'ManageContentController@index'));
    Route::get('content-manager/list/{id?}', array('as' => 'content-manager.list', 'uses' => 'ManageContentController@getList'));
    Route::post('content-manager/store', array('as' => 'content-manager.store', 'uses' => 'ManageContentController@store'));
    Route::get('content-manager/single/{id}', array('as' => 'content-manager.single', 'uses' => 'ManageContentController@getSingle'));
    Route::get('content-manager/remove/{id}', array('as' => 'content-manager.destroy', 'uses' => 'ManageContentController@destroy'));
});


Route::group([
    'middleware' => ['web'],
    'prefix' => 'content-manager', 'namespace' => 'Modules\ContentManager\Http\Controllers'
], function () {

    Route::get("/", array('as' => "content-manager.login", "uses" => "ContentManagerController@login"));
    Route::get("content/{key}", array('as' => "content-manager.listcontentvideos", "uses" => "ContentManagerController@listVideos"));
    Route::post("validatelogin", array('as' => "content-manager.validatelogin", "uses" => "ContentManagerController@validateLogin"));
    Route::get("/videooperations", array('as' => "content-manager.videoOperations", "uses" => "ContentManagerController@videoOperations"));
});
