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
Route::name('s3.multipart.')->namespace('Admin')
    ->group(function () {
    
        Route::post('/s3/multipart', 'UppyS3MultipartController@createMultipartUpload');

        Route::options('/s3/multipart', 'UppyS3MultipartController@createMultipartUploadOptions');

        Route::get('/s3/multipart/{uploadId}', 'UppyS3MultipartController@getUploadedParts');

        Route::get('/s3/multipart/{uploadId}/batch', 'UppyS3MultipartController@prepareUploadParts');

        Route::post('/s3/multipart/{uploadId}/complete', 'UppyS3MultipartController@completeMultipartUpload');

        Route::delete('/s3/multipart/{uploadId}', 'UppyS3MultipartController@abortMultipartUpload');
    });


Route::group(['middleware' => 'web', 'prefix' => 'contentmanager',], function () {
    Route::get('/', 'ContentManagerController@index');
});


Route::group([
    'middleware' => ['web', 'auth'],
    'prefix' => 'admin', 'namespace' => 'Admin'
], function () {
    Route::get('content-manager/s3view/{id?}', array('as' => 'content-manager.s3view', 'uses' => 'ManageContentController@s3index'));
    Route::get('content-manager/s3uploader', array('as' => 'content-manager.s3uploader', 'uses' => 'ManageContentController@s3Uploader'));
    Route::get('content-manager/view', array('as' => 'content-manager.view', 'uses' => 'ManageContentController@index'));
    Route::get('content-manager/list/{id?}', array('as' => 'content-manager.list', 'uses' => 'ManageContentController@getList'));
    Route::post('content-manager/store', array('as' => 'content-manager.store', 'uses' => 'ManageContentController@store'));
    Route::get('content-manager/single/{id}', array('as' => 'content-manager.single', 'uses' => 'ManageContentController@getSingle'));
    Route::get('content-manager/remove/{id}', array('as' => 'content-manager.destroy', 'uses' => 'ManageContentController@destroy'));
});


Route::group([
    'middleware' => ['web'],
    'prefix' => 'content-manager',
], function () {

    Route::get("/", array('as' => "content-manager.login", "uses" => "ContentManagerController@login"));
    Route::get("content/{key}", array('as' => "content-manager.listcontentvideos", "uses" => "ContentManagerController@listVideos"));
    Route::post("validatelogin", array('as' => "content-manager.validatelogin", "uses" => "ContentManagerController@validateLogin"));
    Route::get("/videooperations", array('as' => "content-manager.videoOperations", "uses" => "ContentManagerController@videoOperations"));
});
