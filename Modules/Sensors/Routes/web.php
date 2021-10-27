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

Route::group(['middleware' => ['web', 'auth', 'permission:sensors_admin'], 'prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('sensors/view/{id?}', array('as' => 'sensors.view', 'uses' => 'SensorController@index'));
    Route::get('sensors/list/{id?}', array('as' => 'sensors.list', 'uses' => 'SensorController@getList'));
    Route::post('sensors/store', array('as' => 'sensors.store', 'uses' => 'SensorController@store'));
    Route::get('sensors/single/{id}', array('as' => 'sensors.single', 'uses' => 'SensorController@getSingle'));
    Route::get('sensors/trigger/{id?}', array('as' => 'sensors.trigger', 'uses' => 'SensorController@trigerView'));
    Route::get('sensors/remove/{id}', array('as' => 'sensors.destroy', 'uses' => 'SensorController@destroy'));

});

Route::group(['middleware' => ['web', 'auth', 'permission:sensors_admin']], function () {

    /* Motion Sensor - Start */
    Route::prefix('admin/sensors')
        ->name('motionSensor.')
        ->namespace('Admin')
        ->group(function () {
            Route::get(
                'settings',
                'SensorSettingController@index'
            );
            Route::post(
                'motion-sensor-settings-store',
                array(
                    'as' => 'settings.store',
                    'uses' => 'SensorSettingController@configSettingStore'
                )
            );
            Route::post(
                'sensors/active-setting/store',
                array(
                    'as' => 'active-setting.store',
                    'uses' => 'SensorSettingController@activeSettingStore'
                )
            );
            Route::get(
                'active-setting/list',
                array(
                    'as' => 'active-setting.list',
                    'uses' => 'SensorSettingController@getActiveSettingList'
                )
            );
            Route::get(
                'active-setting/getroomlist/{id}',
                array(
                    'as' => 'active-setting.getroom.list',
                    'uses' => 'SensorSettingController@getRoomList'
                )
            );

            Route::get(
                'active-setting/single/{id}',
                array(
                    'as' => 'active-setting.single',
                    'uses' => 'SensorSettingController@getActiveSettingSingle'
                )
            );


        });

    /* Motion Sensor - End */

});


Route::group(['middleware' => ['web', 'auth','permission:view_sensors'], 'prefix' => 'sensors'], function()
{
    Route::get('sensors/triggers', array('as' => 'sesors.triggers', 'uses' => 'SensorTriggerController@index'));
    Route::get('sensors/triggers/list', array('as' => 'sensors.triggers.list', 'uses' => 'SensorTriggerController@getList'));
});

