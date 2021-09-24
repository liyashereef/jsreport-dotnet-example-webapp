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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'fmdashboard', 'namespace' => 'Modules\FMDashboard\Http\Controllers'], function()
{

    Route::get('facility-management-dashboard', array('as' => 'facility-management-dashboard.index', 'uses' => 'FacilityManagementDashboardController@index'));
    Route::get('facility-management-dashboard-api', array('as' => 'facility-management-dashboard-api', 'uses' => 'FMDashboardAPIController@index'));
    Route::post('facility-management-dashboard-filters', array('as' => 'facility-management-dashboard.filters', 'uses' => 'FMDashboardAPIController@storeFilters'));
    Route::get('facility-management-dashboard-filters', array('as' => 'facility-management-dashboard.filters', 'uses' => 'FMDashboardAPIController@getFilters'));
    Route::post('fm-sync-widget-config',
        array('as' => 'fm-sync-widget-config',
            'uses' => 'FacilityManagementDashboardController@syncWidgetConfig'));
    // Route::get('fm-timesheet-reconciliation/{customer_id?}', array('as'=>'fm-timesheet-reconciliation','uses' => 'FacilityManagementDashboardController@timesheetReconciliation'));
    Route::get('fm-timesheet-reconciliation/{customer_id?}', array('as'=>'fm-timesheet-reconciliation','uses' => 'FMDashboardAPIController@getTimesheetReconciliation'));
    Route::get('fm-training-course-counts', array('as'=>'fm-training-course-counts','uses' => 'FMDashboardAPIController@getCourseAllocatedAndCompletedCount'));
    Route::resource('facility-management-dashboard', 'FacilityManagementDashboardController');

});
