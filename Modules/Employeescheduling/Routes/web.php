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


Route::group(['middleware' => 'web', 'prefix' => 'employeescheduling'], function () {
    Route::get('/', 'EmployeeschedulingController@index');
    Route::get('createschedule/{rejected_id?}', array('as' => 'scheduling.create', 'uses' => 'EmployeeschedulingController@createschedule'));
    Route::get('getProcessedblock', array('as' => 'scheduling.processedblock', 'uses' => 'EmployeeschedulingController@getProcessedblock'));
    Route::get('getProcessedblockspares', array('as' => 'scheduling.processedblockspares', 'uses' => 'EmployeeschedulingController@getProcessedblockspares'));

    Route::get('prepopulatelogs', array('as' => 'scheduling.prepopulatelogs', 'uses' => 'EmployeeschedulingController@prepopulatelogs'));
    Route::post('analysedata', array('as' => 'scheduling.analyseandprocessdata', 'uses' => 'EmployeeschedulingController@setScheduledata'));
    Route::post('saveschedule', array('as' => 'scheduling.saveschedule', 'uses' => 'EmployeeschedulingController@saveSchedule'));
    Route::get('scheduleprecheck', array('as' => 'scheduling.precheck', 'uses' => 'EmployeeschedulingController@precheck'));
    Route::get('saveprecheck', array('as' => 'scheduling.saveprecheck', 'uses' => 'EmployeeschedulingController@saveprecheck'));
    Route::get('removecartentry', array('as' => 'scheduling.removeschedulecartentry', 'uses' => 'EmployeeschedulingController@removecartentry'));
    Route::get('resetscheduleprogress', array('as' => 'scheduling.resetscheduleprogress', 'uses' => 'EmployeeschedulingController@resetscheduleprogress'));

    Route::get('schedulegeneral', array('as' => 'scheduling.schedulegeneralreport', 'uses' => 'EmployeeschedulingController@schedulegeneralreport'));
    Route::post('schedulegeneralresults', array('as' => 'scheduling.schedulegeneralreportresults', 'uses' => 'EmployeeschedulingController@schedulegeneralreportresults'));
    Route::get('payperiodsyearwise', array('as' => 'scheduling.payperiodsyearwise', 'uses' => 'EmployeeschedulingController@payPeriodsYearwise'));

    Route::get('schedulepayperiod', array('as' => 'scheduling.schedulepayperiodreport', 'uses' => 'EmployeeschedulingController@SchedulePayperiodReport'));
    Route::post('schedulepayperiodresults', array('as' => 'scheduling.schedulepayperiodreportresults', 'uses' => 'EmployeeschedulingController@SchedulePayperiodResults'));
    Route::post('schedulepayperiodreportstatus', array('as' => 'scheduling.schedulepayperiodreportstatus', 'uses' => 'EmployeeschedulingController@schedulepayperiodreportstatus'));

    Route::get('scheduleaudit', array('as' => 'scheduling.scheduleaudit', 'uses' => 'EmployeeschedulingController@scheduleaudit'));
    Route::post('scheduleauditresults', array('as' => 'scheduling.scheduleauditresults', 'uses' => 'EmployeeschedulingController@scheduleauditresults'));

    Route::get('scheduleApprovalPage', array('as' => 'scheduling.approval-page', 'uses' => 'EmployeeschedulingController@scheduleApprovalPage'));
    Route::get('getScheduleByStatus', array('as' => 'scheduling.approve-status', 'uses' => 'EmployeeschedulingController@getScheduleByStatus'));
    Route::get('approvalGridView', array('as' => 'scheduling.approval-grid-view', 'uses' => 'EmployeeschedulingController@scheduleApprovalGridView'));
    Route::get('getSheduleDetails', array('as' => 'scheduling.shedule-details', 'uses' => 'EmployeeschedulingController@getSheduleDetails'));
    Route::get('getPayperiodByLastAndPast', array('as' => 'scheduling.getPayperiodByLastAndPast', 'uses' => 'EmployeeschedulingController@getPayperiodByLastAndPast'));
    Route::post('approveSchedule', array('as' => 'scheduling.approve', 'uses' => 'EmployeeschedulingController@approveSchedule'));
    Route::post('rejectSchedule', array('as' => 'scheduling.reject', 'uses' => 'EmployeeschedulingController@rejectSchedule'));
    Route::post('reSchedule', array('as' => 'scheduling.re-schedule-rejected-entry', 'uses' => 'EmployeeschedulingController@reScheduleRejectedSchedule'));
    Route::post('deleteSchedule', array('as' => 'scheduling.delete-schedule', 'uses' => 'EmployeeschedulingController@deleteSchedule'));

    //Compliance widget dataroute
    Route::post('compliancewidgetdata', array('as' => 'scheduling.compliancewidgetdata', 'uses' => 'EmployeeschedulingController@getCompliancewidgetdata'));

    Route::get('schedule-compliance/report', array('as' => 'scheduling.report-non-compliance', 'uses' => 'EmployeeschedulingController@scheduleNonComplianceReport'));
    Route::post('schedule-compliance/report/filter', array('as' => 'scheduling.report-non-compliance-apply-filter', 'uses' => 'EmployeeschedulingController@scheduleNonComplianceReportApplyFilter'));
    Route::get('schedule-compliance/report/managers', array('as' => 'scheduling.regional-managers-by-customer', 'uses' => 'EmployeeschedulingController@getRegionalManagersByCustomer'));
    Route::get('schedule-compliance/report/customers', array('as' => 'scheduling.customers-by-manager', 'uses' => 'EmployeeschedulingController@getCustomersByAreaManager'));
    Route::get('schedule-compliance/report/employees', array('as' => 'scheduling.allocated-employees-by-customer', 'uses' => 'EmployeeschedulingController@getAllocatedEmployeesByCustomer'));

    Route::get('inherit-schedule', array('as' => 'inherit-schedule.index', 'uses' => 'InheritScheduleController@index'));
    Route::get('inherit-schedule/customers', array('as' => 'inherit-schedule.customers', 'uses' => 'InheritScheduleController@customers'));
    Route::get('inherit-schedule/source-payperiod', array('as' => 'inherit-schedule.source-payperiod', 'uses' => 'InheritScheduleController@fetchSourcePayPeriods'));
    Route::get('inherit-schedule/destination-payperiod', array('as' => 'inherit-schedule.destination-payperiod', 'uses' => 'InheritScheduleController@fetchDestinationPayPeriods'));
    Route::post('inherit-schedule/process', array('as' => 'inherit-schedule.process', 'uses' => 'InheritScheduleController@process'));
});
