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

Route::group(['middleware' => 'web', 'prefix' => 'kpi',], function () {
    //TODO:testing purpose
    Route::get('execute-daily-job', 'KpiWidgetController@executeJob'); //TODO::remove later
    Route::get('execute-bulk-job', 'KpiWidgetController@executeBulkJob'); //TODO::remove later
});
