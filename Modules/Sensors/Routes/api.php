<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'v1', 'namespace' => 'Modules\Sensors\Http\Controllers'], function () {
    Route::post('/login', 'LoginController@login')->name('sensors.login');

    // Group with auth and topic
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/motion-start', 'DeviceController@motionStart')
            ->name('sensors.start')
            ->middleware('motion-sensor-topic:start');
        Route::post('/motion-end', 'DeviceController@motionEnd')
            ->name('sensors.end')
            ->middleware('motion-sensor-topic:end');
        Route::post('/device-online', 'DeviceController@online')
            ->name('sensors.online')
            ->middleware('motion-sensor-topic:online');
        Route::post('/device-offline', 'DeviceController@offline')
            ->name('sensors.offline')
            ->middleware('motion-sensor-topic:offline');
        Route::post('/device-low-battery', 'DeviceController@lowBattery')
            ->name('sensors.lowBattery')
            ->middleware('motion-sensor-topic:lowBattery');
        Route::get('/settings', 'DeviceController@settings')->name('sensors.settings');
    });
});
