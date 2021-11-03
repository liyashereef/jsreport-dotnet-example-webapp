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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'employee',], function () {

    // Route::group(['middleware' => ['permission:manage-masters','permission:manage-customers','permission:manage-users']], function () {

    Route::get('timeoff/', array('as' => 'employee.timeoff', 'uses' => 'EmployeeTimeOffController@index'));
    Route::post('timeoff/{module}/store', array('as' => 'timeoff.store', 'uses' => 'EmployeeTimeOffController@store'));
    Route::get('timeoff/edit/{id}', array('as' => 'time-off.edit', 'uses' => 'EmployeeTimeOffController@edit'));

    //});


    Route::get('timeoff/single/{id}', array('as' => 'timeoff.single', 'uses' => 'EmployeeTimeOffController@show'));
    Route::get('timeoff/details', array('as' => 'time-off.details', 'uses' => 'EmployeeTimeoffDetailsController@index'));
    Route::get('timeoff/list', array('as' => 'time-off.list', 'uses' => 'EmployeeTimeoffDetailsController@list'));
    Route::get('timeoff/listSingle/{employee_id}/', array('as' => 'time-off.listSingle', 'uses' => 'EmployeeTimeoffDetailsController@listSingle'));
    Route::post('timeoff/process', array('as' => 'time-off.process', 'uses' => 'EmployeeTimeoffDetailsController@approveOrReject'));
    Route::get('timeoff/getSingle/{id}', array('as' => 'timeoff.getSingle', 'uses' => 'EmployeeTimeoffDetailsController@getSingle'));

    Route::get('timeoff/summary', array('as' => 'timeoff', 'uses' => 'EmployeeSummaryController@index'));
    Route::get('timeoff/summary/list', array('as' => 'timeoff.summary', 'uses' => 'EmployeeSummaryController@summaryList'));
    Route::get('timeoff/absence/summary/{id}', array('as' => 'absence.summaryDetails', 'uses' => 'EmployeeTimeOffController@getCalculatedTimeoff'));

});
