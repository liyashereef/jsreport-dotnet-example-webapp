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

Route::group(['middleware' => ['web', 'auth', 'permission:view_compliance_all|view_analytics|view_compliance'], 'prefix' => 'compliance',], function () {

    Route::get('policy/dashboard', array('as' => 'policy.dashboard', 'uses' => 'ComplianceController@index'));
    Route::get('policy/list/{id?}', array('as' => 'policyTable.list', 'uses' => 'ComplianceController@policyList'));
    Route::get('policy/describe/{boolean}/{id}', array('as' => 'policy.get', 'uses' => 'ComplianceController@policyGet'));
    Route::post('policy/compliant', array('as' => 'policy.compliant', 'uses' => 'ComplianceController@makePolicyCompliant'));
    Route::get('policy/chart/{policy_id}', array('as' => 'policy.chart', 'uses' => 'ComplianceController@getPolicyChart'));
    Route::post('employees/complianceReason', array('as' => 'employee.compliance', 'uses' => 'ComplianceController@getEmployeesComplianceReason'));
    Route::post('employees/pendingCompliance', array('as' => 'pending.compliance', 'uses' => 'ComplianceController@getPendingCompliance'));
    Route::post('policy/uploadimage', array('as' => 'policy.uploadimage', 'uses' => 'ComplianceController@uploadImage'));
});
