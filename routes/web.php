<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'SummaryDashboardController@index');
Route::get('login-access', ['as' => 'loginaccess', 'uses' => 'CandidateLoginAccessController@loginAccess']);
Auth::routes();

Route::get('m360-privacy-policy', function () {
    return view('m360-privacy-policy');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('home', array('as' => 'home', 'uses' => 'WelcomeController@index'));
    Route::get('monitor-dashboard', array('as' => 'monitor-dashboard', 'uses' => 'MonitorDashboardController@index'));
    Route::get('all-incidents/{id?}', array('as' => 'all-incidents', 'uses' => 'MonitorDashboardController@getAllIncidents'));
    Route::get('incident-details/{customer_id}', array('as' => 'incident-details', 'uses' => 'MonitorDashboardController@getCustomerIncident'));
    Route::get('all-noshow/{id?}', array('as' => 'all-noshow', 'uses' => 'MonitorDashboardController@getAllNoShow'));
    Route::get('noshow-details/{customer_id}', array('as' => 'noshow-details', 'uses' => 'MonitorDashboardController@getCustomerNoShow'));
    Route::get('monitor-dashboard/{id}', array('as' => 'monitor-dashboard-reg', 'uses' => 'MonitorDashboardController@viewDashboard'));
    Route::post('file/{module}/store/{custom_name?}', array('as' => 'fileupload', 'uses' => 'AttachmentController@store'));
    Route::get('file/show/{id}/{module}/{attachment?}', array('as' => 'filedownload', 'uses' => 'AttachmentController@show'));
    Route::get('getZipFiles/{fileobj}', array('as' => 'filedownloadzip', 'uses' => 'AttachmentController@getZipFile'));
    Route::get('location/store', array('as' => 'location.store', 'uses' => 'GeoCodeController@storeLatLng'));
    Route::get('myteam', array('as' => 'myteam', 'uses' => 'WelcomeController@getMyTeam'));

    Route::get('training', array('as' => 'training', 'uses' => 'WelcomeController@getTrainingRedirectLink'));
    Route::post('sync-dashboard-filter', 'WelcomeController@syncDashboardFilter')->name('sync-dashboard-filter');
    Route::get('job-ticket-status', array('as' => 'job-ticket-status', 'uses' => 'WelcomeController@getJobTicketStatus'));
    Route::get('candidate-screening-summary', array('as' => 'candidate-screening-summary', 'uses' => 'WelcomeController@getCandidateScreeningSummary'));
    Route::get('guard-tour-list', array('as' => 'guard-tour-list', 'uses' => 'WelcomeController@getGuardTourList'));
    Route::get('site-status-list', array('as' => 'site-status-list', 'uses' => 'WelcomeController@jobMappingForSiteStatus'));
    Route::get('shift-module-details', array('as' => 'shift-module-details', 'uses' => 'WelcomeController@getshiftModuleDetails'));
    Route::get('dashboard-shift-module', array('as' => 'dashboard-shift-module', 'uses' => 'WelcomeController@getAllShiftModules'));

    Route::get('dashboard-site-status-data', array('as' => 'dashboard-site-status-data', 'uses' => 'WelcomeController@getSiteStatusData'));

    //dashboard re-design
    Route::get('dashboard/{customer_id?}', array('as' => 'dashboard', 'uses' => 'WelcomeController@index'));
    Route::get('dashboard-tabs', array('as' => 'dashboard-tabs', 'uses' => 'WelcomeController@getDashboardTabs'));
    Route::get('dashboard-tab-details', array('as' => 'dashboard-tab-details', 'uses' => 'WelcomeController@getDashboardTabDetails'));
    Route::get('dashboard-time-sheet', array('as' => 'dashboard-time-sheet', 'uses' => 'WelcomeController@getDashboardTimeSheet'));
    Route::get('dashboard-employee-schedules', array('as' => 'dashboard-employee-schedules', 'uses' => 'WelcomeController@getEmployeeSchedules'));
    Route::get('dashboard-site-status-map', array('as' => 'dashboard-site-status-map', 'uses' => 'WelcomeController@getSiteStatusMap'));
    Route::get('dashboard-incident-report', array('as' => 'dashboard-incident-report', 'uses' => 'WelcomeController@getDashboardIncidentReport'));
    Route::get('dashboard-visitors-log', array('as' => 'dashboard-visitors-log', 'uses' => 'WelcomeController@getDashboardVisitorsLog'));
    Route::get('dashboard-sitenotes', array('as' => 'dashboard-sitenotes', 'uses' => 'WelcomeController@getDashboardSiteNotes'));
    Route::get('dashboard-training', array('as' => 'dashboard-training', 'uses' => 'WelcomeController@loadDashboardTraining'));
    Route::get('dashboard-post-orders', array('as' => 'dashboard-post-orders', 'uses' => 'WelcomeController@getPostOrders'));
    Route::get('dashboard-key-log', array('as' => 'dashboard-key-log', 'uses' => 'WelcomeController@getKeyLogSummary'));
    Route::get('dashboard-motion-sensor', array('as' => 'dashboard-motion-sensor', 'uses' => 'WelcomeController@getMotionSensorSummary'));

    //new landing widgets
    Route::get('landing-page', array('as' => 'landing-page-tabs', 'uses' => 'WelcomeController@getLandingPageTabs'));
    Route::get('landing-widget-site-summary', array('as' => 'landing-widget-site-summary', 'uses' => 'LandingWidgetController@getSiteSummary'));
    Route::get('landing-widget-trend-analysis', array('as' => 'landing-widget-trend-analysis', 'uses' => 'LandingWidgetController@getTrendAnalysis'));
    Route::get('landing-widget-site-matrix', array('as' => 'landing-widget-site-matrix', 'uses' => 'LandingWidgetController@getSiteMatrix'));
    Route::get('landing-widget-site-details', array('as' => 'landing-widget-site-details', 'uses' => 'LandingWidgetController@getSiteDetails'));
    Route::get('landing-widget-schedule-compliance', array('as' => 'landing-widget-schedule-compliance', 'uses' => 'LandingWidgetController@getScheduleCompliance'));
    Route::get('landing-widget-incident-compliance', array('as' => 'landing-widget-incident-compliance', 'uses' => 'LandingWidgetController@getIncidentResponseCompliance'));
    Route::get('landing-widget-elavator-entrapment-responce', array('as' => 'landing-widget-elavator-entrapment-responce', 'uses' => 'LandingWidgetController@getElavatorEntrapmentResponce'));
    Route::get('landing-widget-incident-kpi', array('as' => 'landing-widget-incident-kpi', 'uses' => 'LandingWidgetController@getIncidentResponceKpi'));
    Route::get('landing-widget-training/{mandatory?}/{spares?}', array('as' => 'training-course-widget', 'uses' => 'LandingWidgetController@getTrainingWidget'));
    Route::get('landing-widget-shift-journal-summary', array('as' => 'landing-widget-shift-journal-summary', 'uses' => 'LandingWidgetController@getShiftModulePostOrder'));
    Route::get('landing-widget-timesheet-reconciliation', array('as' => 'landing-widget-timesheet-reconciliation', 'uses' => 'LandingWidgetController@getTimesheetReconciliation'));
    Route::get('landing-widget-motion-sensor', array('as' => 'landing-widget-motion-sensor', 'uses' => 'LandingWidgetController@getMotionSensorDetails'));
    Route::get('landing-widget-client-survey', array('as' => 'landing-widget-client-survey', 'uses' => 'LandingWidgetController@getClientSurvey'));
    Route::get('landing-widget-client-survey-analytics', array('as' => 'landing-widget-client-survey-analytics', 'uses' => 'LandingWidgetController@getClientSurveyAnalytics'));
    Route::get('landing-widget-qr-patrol', array('as' => 'landing-widget-qr-patrol', 'uses' => 'LandingWidgetController@getQrPatrolDetails'));
    Route::get('landing-widget-scheduling', array('as' => 'landing-widget-scheduling', 'uses' => 'WelcomeController@getScheduleDetails'));
    Route::get('landing-widget-client-concern', array('as' => 'landing-widget-client-concern', 'uses' => 'WelcomeController@getClientConcern'));
    Route::get('landing-widget-client-feedback', array('as' => 'landing-widget-client-feedback', 'uses' => 'WelcomeController@getClientFeedback'));

    //summary dashboard
    Route::get('landing-widget-key-performance-indicator', array('as' => 'landing-widget-key-performance-indicator', 'uses' => '\Modules\KPI\Http\Controllers\KpiWidgetController@index'));
    Route::get('summary-dashboard', array('as' => 'summary-dashboard.index', 'uses' => 'SummaryDashboardController@index'));
    Route::get('summary-dashboard/client-survey', array('as' => 'summary-dashboard.client-survey', 'uses' => 'SummaryDashboardController@loadClientSurvey'));
    Route::get('summary-dashboard/employee-survey', array('as' => 'summary-dashboard.employee-survey', 'uses' => 'SummaryDashboardController@loadEmployeeSurvey'));
    Route::get('summary-dashboard/operations-dashboard-matrix', array('as' => 'summary-dashboard.operations-dashboard-matrix', 'uses' => 'SummaryDashboardController@loadOperationsDashboardMatrix'));
    Route::get('summary-dashboard/safety-dashboard-matrix', array('as' => 'summary-dashboard.safety-dashboard-matrix', 'uses' => 'SummaryDashboardController@loadSafetyDashboardMatrix'));
    Route::get('summary-dashboard/kpi-tile-blocks', array('as' => 'summary-dashboard.kpi-tile-blocks', 'uses' => 'SummaryDashboardController@kpiTileBlocks'));
    Route::get('summary-dashboard/tile-blocks', array('as' => 'summary-dashboard.tile-blocks', 'uses' => 'SummaryDashboardController@summaryTileBlocks'));
    Route::get('summary-dashboard/work-hours-earned-billing-details', array('as' => 'summary-dashboard.work-hours-earned-billing-details', 'uses' => 'SummaryDashboardController@loadBillingWorkHourDetails'));

    Route::get('guard-perfomance', array('as' => 'guard-perfomance', 'uses' => 'SummaryDashboardController@guardPerfomanceInner'));
    Route::get('guard-perfomance-info', array('as' => 'guard-perfomance-info', 'uses' => 'SummaryDashboardController@guardPerfomanceInfo'));
    Route::get('training-compliance-inner', array('as' => 'training-compliance-inner', 'uses' => 'SummaryDashboardController@trainingComplianceInner'));
    Route::post('guard-perfomance-details', array('as' => 'guard-perfomance-details', 'uses' => 'SummaryDashboardController@guardPerfomanceDetails'));
});
