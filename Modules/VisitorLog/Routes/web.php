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

Route::prefix('visitorlog')->group(function () {
    Route::get('/', 'VisitorLogController@index');
});

Route::group(
    [
        'middleware' => 'web',
        'prefix' => 'admin/visitor-log',
        'namespace' => 'Admin'
    ],
    function () {
        Route::get('devices', array('as' => 'visitor-log.devices', 'uses' => 'VisitorLogDeviceController@index'));
        Route::get('device/list', array('as' => 'visitor-log.device.lists', 'uses' => 'VisitorLogDeviceController@getAll'));
        Route::get('allocated/template/{customerId}', array('as' => 'visitor-log.template-allocated', 'uses' => 'VisitorLogDeviceController@getAllocatedTemplates'));
        Route::post('devices/store', array('as' => 'visitor-log.device.store', 'uses' => 'VisitorLogDeviceController@store'));
    }
);
