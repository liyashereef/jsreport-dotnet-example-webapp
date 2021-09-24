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

Route::group(['namespace' => 'Modules\KeyManagement\Http\Controllers'], function() {
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/keyinfo', 'KeyManagementController@getKeyInfo')->name('keymanagement.keyinfo');
        Route::post('/getidtypes', 'KeyManagementController@getIdTypes')->name('keymanagement.getkeyroomname');
        Route::post('/getkeyroomname', 'KeyManagementController@getKeyLookup')->name('keymanagement.getidtypes');
        Route::post('/keydetails', 'KeyManagementController@getKeyDetails')->name('keymanagement.keydetails');
        Route::get('file/show/{id}/{module}/{attachment?}', '\App\Http\Controllers\Common\AttachmentController@show')
            ->name('keymanagement.filedownload');
        Route::post('keylog/store', 'KeyManagementController@storeCheckout')->name('keymanagement.keylog');
        Route::post('keylog/store/checkin', 'KeyManagementController@storeCheckin')->name('keymanagement.keylog.checkin');

    });
});
