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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'reports',], function () {

    Route::get('/', array('as' => 'reports', 'uses' => 'ReportsController@index'));
    Route::get('dailytransactions', array('as' => 'reports.dailytransactions', 'uses' => 'CovidReportController@getDailyTransactions'));
    Route::get('fevercompliancereport', array('as' => 'reports.fevercompliancereport', 'uses' => 'CovidReportController@getFeverComplianceReport'));

    Route::get('covid-report', array('as' => 'reports.covidreport', 'uses' => 'CovidReportController@covidReport'));
    Route::get('get-covid-report', array('as' => 'reports.getCovidReport', 'uses' => 'CovidReportController@getCovidReport'));
    Route::get('compliancegraph', array('as' => 'reports.compliancegraph', 'uses' => 'CovidReportController@complianceReport'));
    Route::post('getcompliancegraph', array('as' => 'reports.getcompliancegraph', 'uses' => 'CovidReportController@getComplianceReport'));
    /* Site Notes */
    Route::get('sitenotes', array('as' => 'reports.sitenotes', 'uses' => 'SiteNotesController@siteNotes'));
    Route::post('customer/getsitenotes', array('as' => 'customer.getsitenotes', 'uses' => 'SiteNotesController@getSiteNotes'));
    /* Site Notes -End */

    // Customer Survey Report
    Route::get('surveryreport', array('as' => 'reports.surveryreport', 'uses' => 'CustomerSurveyReportController@surveryreport'));
    Route::post('getsurveryreport', array('as' => 'reports.getsurveryreport', 'uses' => 'CustomerSurveyReportController@getsurveryreport'));
    // Customer Survey Report

    // Visitor Log Report
    Route::get('visitorlogreport', array('as' => 'reports.visitorLogReport', 'uses' => 'VisitorLogReportController@visitorLogReport'));
    Route::get('getvisitorlogreport', array('as' => 'reports.getVisitorLogReport', 'uses' => 'VisitorLogReportController@getVisitorLogReport'));
    //Visitor Log Report

    //Certificate Expiry Report starts
    Route::middleware(['permission:view_all_site_document_report|view_allocated_site_document_report'])->group(function () {
        Route::get('certificateexpiryreport', array('as' => 'reports.certificateExpiryReport', 'uses' => 'CertificateExpiryReportController@certificateExpiryReport'));
        Route::get('getcertificateexpiryreport', array('as' => 'reports.getCertificateExpiryReport', 'uses' => 'CertificateExpiryReportController@getcertificateexpiryreport'));
        Route::get('documentexpiryreportemailtemplate', array('as' => 'reports.documentExpiryReportTemplate', 'uses' => 'CertificateExpiryReportController@getSingle'));
        Route::post('documentexpirysendmail', array('as' => 'reports.expiryMail', 'uses' => 'CertificateExpiryReportController@sendExpiryMail'));
    });
    //Certificate Expiry Report ends

    // Candidate Onboarding Status Report - Start
    Route::group(['middleware' => ['permission:view_recruiting_analytics_report']], function () {
        Route::get('recruitinganalyticsreport', array('as' => 'reports.recruitinganalyticsreport', 'uses' => 'RecruitingAnalyticsController@recruitingAnalyticsReport'));
        Route::post('recruitinganalyticsreport/list', array('as' => 'reports.recruitinganalyticsreport.list', 'uses' => 'RecruitingAnalyticsController@recruitingAnalyticsReportList'));
        Route::get('recruitinganalyticsreport/excel', array('as' => 'reports.recruitinganalyticsreport.excel', 'uses' => 'RecruitingAnalyticsController@recruitingAnalyticsExcelReport'));
    });
    // Candidate Onboarding Status Report - End

    // Termination Report - Start
    Route::group(['middleware' => ['permission:view_termination_report']], function () {
        Route::get('terminationreport', array('as' => 'reports.terminationReport', 'uses' => 'TerminationReportController@terminationReport'));
        Route::post('getterminationreport', array('as' => 'reports.getTerminationReport', 'uses' => 'TerminationReportController@getTerminationReport'));
    });
    // Termination Report - End
});
