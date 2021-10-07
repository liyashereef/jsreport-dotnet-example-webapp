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

Route::group(['middleware' => ['web', 'auth', 'permission:view_supervisorpanel'], 'prefix' => 'supervisorpanel'], function () {

    Route::get('customers/mapping/{stc?}', array('as' => 'customers.mapping', 'uses' => 'SupervisorPanelController@index'));
    Route::get('average/color-name/{avg}', array('as' => 'customers.average-color-name', 'uses' => 'SupervisorPanelController@getColorByAverage'));
    Route::get('customer/details/{id}/{payperiod_id?}/{analytics?}', array('as' => 'customer.details', 'uses' => 'SupervisorPanelController@customerDetails'));
    Route::get('customer/sitenotes/{id}/{siteNotesId}', array('as' => 'customer.sitenotesonly', 'uses' => 'SupervisorPanelController@siteNotesDetails'));

    Route::middleware(['permission:view_guard_tour|view_all_guard_tour|view_shift_journal|view_all_shift_journal|view_allocated_shift_journal'])->group(function () {
        Route::get('customers/{guard_tour?}/mapping', array('as' => 'customers.mappingGuardTour', 'uses' => 'SupervisorPanelController@guardTourMap'));
        Route::get('customer/shiftdetails/{id}', array('as' => 'customer.guardTourDetails', 'uses' => 'SupervisorPanelController@customerGuardTourDetails'));
        Route::get('customer/guardTourlist/{shift_id}', array('as' => 'guardTour.list', 'uses' => 'SupervisorPanelController@guardTourList'));
        Route::get('customer/guardTourlistCustomerid', array('as' => 'guardTourlist.customerid', 'uses' => 'SupervisorPanelController@getLatestGuardTourCustomerId'));
        Route::get('customer/{guard_tour_id?}/{image_id?}/guardTour/downloadfile', array('as' => 'guardTour.attachement', 'uses' => 'SupervisorPanelController@getfiles'));
    });

    Route::middleware(['permission:view_all_shift_module_mapping|view_allocated_shift_module_mapping'])->group(function () {
        Route::get('shiftmodule/mapping', array('as' => 'shiftmodule.mapping', 'uses' => 'SupervisorPanelController@shiftModuleMap'));
        Route::get('project/modulelist/{id?}', array('as' => 'project.modulelist', 'uses' => 'SupervisorPanelController@getAllShiftModules'));
        Route::get('shiftmodule/mappinglist/{id?}/{module_id?}/{date?}', array('as' => 'shiftmodule.mappinglist', 'uses' => 'SupervisorPanelController@getAllShiftModulesMapping'));
    });


    Route::middleware(['permission:view_shift_journal|view_all_shift_journal|view_allocated_shift_journal'])->group(function () {
        Route::get('customer/shiftjournaldetails/{id}', array('as' => 'customer.shiftJournalDetails', 'uses' => 'SupervisorPanelController@customerShiftJournalDetails'));
        Route::get('customer/shiftJournallist/{shift_id}', array('as' => 'shiftJournal.list', 'uses' => 'SupervisorPanelController@shiftJournalList'));
        Route::post('customer/shiftJournalSave/store', array('as' => 'shiftJournal.save', 'uses' => 'SupervisorPanelController@saveShiftJournalWeb'));
    });
    //Shift Module
    Route::middleware(['permission:view_shift_journal_20_transaction|view_all_shift_journal_20_transaction'])->group(function () {
        Route::get('shiftModule/list/{module_id}/{customer_id}', array('as' => 'shift.module', 'uses' => 'SupervisorPanelController@shiftModuleList'));
    });
    Route::get('timeShift/list/{customer_id}', array('as' => 'timeshift.list', 'uses' => 'SupervisorPanelController@timeshiftList'));
    //Shift Module
    //
    Route::get('customer/{customer_id?}/{payperiod_id?}/report', array('as' => 'customer.report', 'uses' => 'SupervisorPanelController@customerPayperiodReport'));
    Route::get('customer/{customer_id?}/{payperiod_id?}/edit', array('as' => 'customer.reportedit', 'uses' => 'SupervisorPanelController@customerPayperiodReportEdit'));
    Route::post('customer/report/store', array('as' => 'report.store', 'uses' => 'SupervisorPanelController@customerPayperiodReportStore'));

    Route::middleware(['permission:view-trend-report'])->group(function () {
        Route::get('customer/{customer_id?}/{payperiod_start?}/{payperiod_end?}/trendreport', array('as' => 'customer.trendreport', 'uses' => 'SupervisorPanelController@customerPayperiodTrendReport'));
    });
    // Site Notes
    Route::get('customer/{customer_id?}/{note_id?}/sitenote', array('as' => 'customer.sitenotes', 'uses' => 'SupervisorPanelController@siteNotesIndex'));
    Route::post('customer/{customer_id?}/{note_id?}/sitenote/save', array('as' => 'customer.sitenotes.save', 'uses' => 'SupervisorPanelController@saveSiteNote'));

    //Route::get('supervisor-panel/customer-report', array('as' => 'supervisor-panel.customer-report', 'uses' => 'Front\SupervisorPanelController@customerReport'));
    Route::middleware(['permission:create-incident-report'])->group(function () {
        Route::post('customer/{customer_id?}/{payperiod_id?}/incident', array('as' => 'incident.store', 'uses' => 'IncidentReportController@store'));
        Route::post('customer/incident-status-change', array('as' => 'incident.status', 'uses' => 'IncidentReportController@storeStatusChange'));
    });

    Route::get('incident/{id?}/view', array('as' => 'incident.details', 'uses' => 'IncidentReportController@incidentDetails'));
    Route::get('incidentStatusList/{id?}/view', array('as' => 'incident.incidentStatusList', 'uses' => 'IncidentReportController@incidentStatusLists'));

    Route::middleware(['permission:view_all_incident_report|view_allocated_incident_report'])->group(function () {
        Route::get('incident/dashlist', array('as' => 'incident.dashboard.list', 'uses' => 'IncidentReportController@getIncidentReportDashboardList'));
        Route::get('incident/dashboard', array('as' => 'incident.dashboard', 'uses' => 'IncidentReportController@incidentReportDashboard'));
    });
    Route::middleware(['permission:create-incident-report|view_all_incident_report|view_allocated_incident_report'])->group(function () {
        Route::get('customer/{customer_id?}/{payperiod_id?}/incident', array('as' => 'incident.init', 'uses' => 'IncidentReportController@index'));
        Route::get('customer/{customer_id?}/{payperiod_id?}/incident/list', array('as' => 'incident.list', 'uses' => 'IncidentReportController@getList'));
        Route::get('customer/{incident_report_id?}/incident/downloadfile', array('as' => 'incident.attachement', 'uses' => 'IncidentReportController@getfile'));
    });
    Route::post('customers/rating', array('as' => 'customers.rating.store', 'uses' => 'SupervisorPanelController@customerRatingStore'));
    Route::get('customers/score', array('as' => 'customers.score.list', 'uses' => 'SupervisorPanelController@getCustomerScoreList'));
    Route::get('customers/more-details', array('as' => 'customers.more-details', 'uses' => 'SupervisorPanelController@getCustomerMoreDetails'));

    Route::middleware(['permission:view_operational_dashboard'])->group(function () {
        Route::get('operational-dashboard', array('as' => 'operational-dashboard', 'uses' => 'OperationalDashboardController@index'));
        Route::get('operational-dashboard/parent_answers/{template_category_id}', array('as' => 'operational-dashboard.parent_answers', 'uses' => 'OperationalDashboardController@getTemplateCategoryParentQuestionAnswers'));
    });

    Route::get('stc/geo-mapping', array('as' => 'stc-schedule.geo-mapping', 'uses' => 'StcScheduleGeoMappingController@index'));
    Route::get('stc/geo-mapping-details', array('as' => 'stc-schedule.geo-mapping-details', 'uses' => 'StcScheduleGeoMappingController@fetchStcSiteDetails'));
    Route::get('stc/geo-mapping-by-customer', array('as' => 'stc-schedule.geo-mapping-by-customer', 'uses' => 'StcScheduleGeoMappingController@showStcScheduleDetails'));
});
