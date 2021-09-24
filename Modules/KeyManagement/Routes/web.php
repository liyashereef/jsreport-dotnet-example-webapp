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

Route::group(['middleware' => ['web', 'auth','permission:view_keymanagement'], 'prefix' => 'keymanagement', 'namespace' => 'Modules\KeyManagement\Http\Controllers'], function()
{
    Route::group(['middleware' => ['permission:view_all_customers_keys|view_allocated_customers_keys']], function () {
        Route::name('key-setting')->get('key-setting', 'CustomerKeyDetailController@index');
        Route::get('key-setting/keylist/{id}', array('as' => 'keysetting.keylist', 'uses' => 'CustomerKeyDetailController@createKeyDetails'));
        Route::get('key-setting/list/{id}', array('as' => 'keysetting.list', 'uses' => 'CustomerKeyDetailController@getList'));
        Route::get('key-setting/customer/list', array('as' => 'keysetting.customer.list', 'uses' => 'CustomerKeyDetailController@getCustomerList'));
        Route::post('key-setting/store', array('as' => 'keysetting.store', 'uses' => 'CustomerKeyDetailController@store'));
        Route::get('key-setting/single/{id}', array('as' => 'keysetting.single', 'uses' => 'CustomerKeyDetailController@getSingle'));
        Route::get('key-setting/remove/{id}', array('as' => 'keysetting.destroy', 'uses' => 'CustomerKeyDetailController@destroy'));
    });

    Route::group(['middleware' => ['permission:view_all_keylog_summary|view_allocated_keylog_summary']], function () {
        Route::get('key-setting/keylogs', array('as' => 'keysetting.keylog', 'uses' => 'CustomerKeyDetailController@createKeyLog'));
        Route::get('key-setting/keylog/list', array('as' => 'keysetting.keylog.list', 'uses' => 'CustomerKeyDetailController@getKeyLogList'));
        Route::get('key-setting/keylog/single/{id}', array('as' => 'keysetting.keylog.single', 'uses' => 'CustomerKeyDetailController@getKeyLogSingle'));
    });
});
