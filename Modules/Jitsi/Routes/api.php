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

Route::group(['middleware' => ['checkIp'], 'namespace' => 'Modules\Jitsi\Http\Controllers\API'], function () {
    Route::post('setJibriconferencestatus', 'JitsiApiController@setJibriconferencestatus');
    Route::post('saveRecording', "JitsiApiController@saveRecording");
    Route::post('getIdlerecordingserver', 'JitsiApiController@getIdlerecordingserver');
    Route::post('getShutdownprocedure', 'JitsiApiController@getShutdownprocedure');
    Route::post('rebootServer', 'JitsiApiController@rebootServer');
    Route::post('updateconferencecount', 'JitsiApiController@updateConferencecount');
});
Route::group(['middleware' => ['auth:api'], 'namespace' => 'Modules\Jitsi\Http\Controllers\API'], function () {
    Route::post('getJibriConferenceStatus', 'JitsiApiController@getJibriConferenceStatus');
    Route::post('getJitsiOwner', 'JitsiApiController@getJitsiOwner');
});
