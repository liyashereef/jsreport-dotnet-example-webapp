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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'feverscan',], function()
{
    Route::get('/', 'FeverScanController@index');
    Route::get('site-view', array('as' => 'fever.site-view', 'uses' => 'FeverScanController@siteView'));
    Route::get('individual-view', array('as' => 'fever.individual-view', 'uses' => 'FeverScanController@individualView'));
    Route::get('customer-fever-reading', array('as' => 'customer-fever-reading', 'uses' => 'FeverScanController@getCustomerFeverReadinginfo'));
    Route::get('fever-reading-reports', array('as' => 'fever-reading-report-view', 'uses' => 'FeverScanController@getFeverReadingReportView'));
    Route::get('fever-reading-report-data', array('as' => 'fever-reading-report-data', 'uses' => 'FeverScanController@getFeverReadingReportData'));
    //Route::get('install', array('as' => 'install', 'uses' => 'FeverScanController@setFeverScanModule'));
    Route::get('alter', array('as' => 'install', 'uses' => 'FeverScanController@alterFeverScanModule'));

});
