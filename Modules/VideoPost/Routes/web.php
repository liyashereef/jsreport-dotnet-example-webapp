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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'videopost'], function()
{
    Route::group(['middleware' => ['permission:view_video_post_summary']], function () {
        Route::get('videopost/summary', array('as' => 'videopost.summary', 'uses' => 'VideoPostController@showVideoPost'));
        Route::get('videopost/filedownload', array('as' => 'videopost.filedownload', 'uses' => 'VideoPostController@getVideoUrl'));
        Route::get('videopost/summary/list', array('as' => 'videopost.summary.list', 'uses' => 'VideoPostController@getVideoPostlist'));
    });

    Route::group(['middleware' => ['permission:add_video_post|edit_video_post']], function () {
        Route::get('videopost', array('as' => 'videopost', 'uses' => 'VideoPostController@index'));
        Route::get('videopost/edit/{id?}', array('as' => 'video-post.edit', 'uses' => 'VideoPostController@editVideoPost'));
        Route::post('videopost/store', array('as' => 'videopost.store', 'uses' => 'VideoPostController@storeVideoPosting'));
    });

    Route::group(['middleware' => ['permission:delete_video_post']], function () {
        Route::get('videopost/destroy', array('as' => 'videopost.destroy', 'uses' => 'VideoPostController@destroy'));
    });

});

