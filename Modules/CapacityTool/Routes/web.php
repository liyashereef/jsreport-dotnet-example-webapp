<?php

Route::group(['middleware' => ['web', 'auth', 'permission:view_capacitytool'], 'prefix' => 'capacitytool',], function () {
    Route::get('/', array('as' => 'capacitytool', 'uses' => 'CapacityToolController@index'));

    Route::group(['middleware' => ['permission:create_entry']], function () {
        Route::get('create', array('as' => 'capacitytool.create', 'uses' => 'CapacityToolController@create'));
        Route::post('store', array('as' => 'capacitytool.store', 'uses' => 'CapacityToolController@store'));
    });
    Route::group(['middleware' => ['permission:edit_capacity_tool']], function () {
        Route::get('{id}/edit', array('as' => 'capacitytool.edit', 'uses' => 'CapacityToolController@edit'));
    });
    Route::get('list', array('as' => 'capacitytool.list', 'uses' => 'CapacityToolController@getList'));
    Route::get('destroy', array('as' => 'capacitytool.destroy', 'uses' => 'CapacityToolController@destroy'));
    Route::get('{id}', array('as' => 'capacitytool.show', 'uses' => 'CapacityToolController@show'));
    Route::post('subquestion', array('as' => 'capacitytool.subquestion', 'uses' => 'CapacityToolController@subquestion'));

});
