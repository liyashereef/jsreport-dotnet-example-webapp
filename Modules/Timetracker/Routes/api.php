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

Route::group([], function () {
    Route::group(['middleware' => ['login-validation-log']], function () {
        Route::post('login', 'API\v1\ApiController@login')->name('app.login');
    });

    Route::post('forgotPassword', 'API\v1\ApiController@forgotPassword');

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('syncdata', 'API\v1\ApiController@syncdata');
        Route::post('submitshift', 'API\v1\ApiController@submitShift');
        // Route::post('submitshiftjournal', 'API\v1\ApiController@submitShiftJournal');
        Route::post('submitshiftjournal', 'API\v1\ApiController@submitShiftJournal');
        Route::post('submitTimeSheet', 'API\v1\ApiController@submitTimeSheet');
        Route::post('approveTimeSheet', 'API\v1\ApiController@approveTimeSheet');
        Route::post('approvedRequests', 'API\v1\ApiController@getApprovedRequests');
        Route::post('editProfile', 'API\v1\ApiController@editprofile');
        Route::post('test', 'API\v1\ApiController@test');
        Route::post('logout', 'API\v1\ApiController@logout');
        Route::post('getGuards', 'API\v1\ApiController@getGuards');
        Route::post('getGuardDetails', 'API\v1\ApiController@getGuardDetails');
        Route::post('submitIncidentReport', 'API\v1\ApiController@submitIncidentReport');
        Route::post('submitSecurityPatrol', 'API\v1\ApiController@submitSecurityPatrol');
        Route::post('getCustomerModuleDetails', 'API\v1\ApiController@getCustomerModuleDetails');
        Route::post('submitCustomerModuleDetails', 'API\v1\ApiController@submitCustomerModuleDetails');
        Route::post('storeVideo', 'API\v1\ApiController@storeVideo');
        Route::post('saveFile', 'API\v1\ApiController@saveFile');
        Route::post('userDetails', 'API\v1\ApiController@getUserDetails');

        //Routes for Employee availability
        Route::post('saveEmployeeavailability', 'API\v1\ApiController@setEmployeeavailability');
        //Routes for Employee availability
        //Routes for retrieve shift timings
        Route::post('getShifttiming', 'API\v1\ApiController@getShiftTimings');
        //Routes for retrieve shift timings
        //Routes for retrieve Work days
        Route::post('getEmployeeworkdays', 'API\v1\ApiController@getWorkdays');
        //Routes for retrieve Work days
        //Routes for retrieve Work days and shifts
        Route::post('getNonAvailability', 'API\v1\ApiController@getNonAvailability');
        //Routes for retrieve Work days and shifts
        //Save unavailability
        Route::post('setUnAvailability', 'API\v1\ApiController@setUnAvailability');
        //Save unavailability
        //remove unavailability
        Route::post('removeUnAvailability', 'API\v1\ApiController@removeUnAvailability');
        //remove unavailability
        //Routes for retrieve Work days and shifts
        Route::post('getUnAvailability', 'API\v1\ApiController@getUnAvailability');
        //Routes for retrieve Work days and shifts
        //Routes for getting employee current shifts
        Route::post('getEmployeeshift', 'API\v1\ApiController@getEmployeeshift');
        //Routes for getting employee current shifts

        //Routes for getting user customer allocation
        Route::post('customer/vlog-templates', 'API\v1\ApiController@getCustomerUserAllocation');
        //Routes for getting user customer allocation
        //Routes for getting Customers vistor log template
        Route::post('customer/{customer_id}/visitor_logs', 'API\v1\ApiController@getCustomerVisitorlogTemplates');

        //Routes for getting Time off reasons

        Route::post('employee/timeoffreasons', 'API\v1\ApiController@getEmployeetimeoffreasons');
        Route::post('employee/savetimeoff', 'API\v1\ApiController@saveEmployeeTimeOff');
        Route::post('employee/cpidByCustomer', 'API\v1\ApiController@getCpidByCustomer');
        //Routes for getting Time off reasons


        /*Route for Open shift */
        Route::post('getOpenShift', 'API\v1\ApiController@getOpenShift');
        Route::post('getOpenShiftdetail', 'API\v1\ApiController@getOpenShiftdetail');
        Route::post('getOpenShiftdetailview', 'API\v1\ApiController@getOpenShiftdetailview');
        Route::post('Employeedetails', 'API\v1\ApiController@getEmployeedetails');


        Route::post('setOpenShift', 'API\v1\ApiController@setOpenShift');
        /*Route for Open shift */
        Route::post('startshift', 'API\v1\ApiController@startShift');
        Route::post('endshift', 'API\v1\ApiController@endShift');
        Route::post('meetingNotes', 'API\v1\ApiController@submitShiftNotes');
        Route::post('shiftLiveLocation', 'API\v1\ApiController@shiftLiveLocation');
        //Routes creating for Qrcode based apis
        Route::post('qrCodeWithShift', 'API\v1\ApiController@submitQrcodeWithShift');
        Route::post('getCustomerDetails', 'API\v1\ApiController@getCustomerDetails');

        //For Live Location Update on MongoDB
        Route::post('storeLiveLocation', 'API\v1\ApiController@storeLiveLocation');

        //Dispatch Section
        Route::get('dispatch_request_types', 'API\v1\DispatchRequestTypeApiController@list');

        Route::post('storeFCMToken', 'API\v1\UserDevicesController@storeFCMToken');
        Route::post('getAllUserDevice', 'API\v1\UserDevicesController@getAllUserDevice');
        Route::post('getUserDevice', 'API\v1\UserDevicesController@getUserDevice');
        Route::post('dispatch_request/all', 'API\v1\DispatchRequestApiController@getAllByStatus');
        Route::post('dispatch_request/status_array', 'API\v1\DispatchRequestApiController@getAllByStatusArray');
        Route::post('getAllMyRequest', 'API\v1\DispatchRequestApiController@getAllMyRequest');
        Route::post('getMstDetailsById', 'API\v1\DispatchRequestApiController@getDetailsById');
        Route::post('mstRequestStatusUpdate', 'API\v1\DispatchRequestApiController@mstRequestStatusUpdate');
        Route::post('mstDeclineRequest', 'API\v1\DispatchRequestApiController@declineRequest');
        Route::post('mstNotifications', 'API\v1\DispatchRequestApiController@getDispatchRequestNotifications');

        //Employee Feedback
        Route::post('getDepartments', 'API\v1\EmployeeFeedbackController@getDepartments');
        Route::post('submitEmployeeFeedback', 'API\v1\EmployeeFeedbackController@submitEmployeeFeedback');

        //expense module
        Route::post('submitExpenseClaim', 'API\v1\ApiController@submitExpenseClaim');
        Route::post('getExpenseClaim', 'API\v1\ApiController@getExpenseClaim');

        //expense module Mileage Claim
        Route::post('submitMileageClaim', 'API\v1\ApiController@submitMileageClaim');
        Route::post('getVehicleLists', 'API\v1\ApiController@getVehicleLists');
        Route::post('getFlatRates', 'API\v1\ApiController@getFlatRates');
        Route::post('myTimeSheet', 'API\v1\ApiController@generateMyTimeSheet');
        //latest trip details
        Route::post('latestTripDetails', 'API\v1\ApiController@latestTripDetails');
        //Vehicle Module
        Route::post('getAllVehicles', 'API\v1\VehicleController@getAllVehicles');
        Route::post('submitVehicleTrips', 'API\v1\VehicleController@submitVehicleTrips');

        Route::post('getAllocatedCustomers', 'API\v1\ApiController@getAllocatedCustomers');
        Route::post('getAllocatedUsers', 'API\v1\ApiController@getAllocatedUsers');
        Route::post('getEmployeeRatingLookup', 'API\v1\ApiController@getEmployeeRatingLookup');
        Route::post('getEmployeePolicyLookup', 'API\v1\ApiController@getRatingPolicyLookup');
        Route::post('submitEmployeeRating', 'API\v1\ApiController@submitEmployeeRating');
        Route::post('getEmployeeRating', 'API\v1\ApiController@getEmployeeRating');
        Route::post('getEmployeeRatingBySupervisor', 'API\v1\ApiController@getEmployeeRatingBySupervisor');
        Route::post('submitRatingResponse', 'API\v1\ApiController@submitRatingResponse');
        Route::post('getAllPushNotifications', 'API\v1\ApiController@getAllPushNotifications');
        Route::post('getUnreadNotifications', 'API\v1\ApiController@getUnreadNotifications');
        Route::post('updatePushNotificationReadFlag', 'API\v1\ApiController@updatePushNotificationReadFlag');
        Route::post('getCertificateExpiryReminderNotifications', 'API\v1\ApiController@getCertificateExpiryReminderNotifications');
        Route::post('getGuardCompliance', 'API\v1\ApiController@getGuardComplianceDashboard');
        Route::post('submitTimesheetApprovalRatingResponse', 'API\v1\ApiController@submitTimesheetApprovalRatingResponse');


        /*Retrieve all open Employee survey against a user */
        Route::post('getEmployeeWiseRatings', 'API\v1\EmployeeSurveyController@getEmployeeWiseRatings');
        /*Retrieve template for a survey */
        Route::post('getTemplatedetail', 'API\v1\EmployeeSurveyController@getTemplatedetail');
        /*Submit Survey details*/
        Route::post('submitEmployeeSurvey', 'API\v1\EmployeeSurveyController@submitEmployeeSurvey');
        /*Submit Survey details*/
        Route::post('getSurveyDetails', 'API\v1\EmployeeSurveyController@getSurveyDetails');

        // Employee Whistle Blower
        Route::post('getWhistleblowerCategory', 'API\v1\ApiController@getWhistleblowerCategory');
        Route::post('getWhistleblowerPriority', 'API\v1\ApiController@getWhistleblowerPriority');
        Route::post('getEmployeePolicies', 'API\v1\ApiController@getEmployeePolicies');
        Route::post('submitEmployeeWhistleblower', 'API\v1\ApiController@submitEmployeeWhistleblower');
        Route::post('getUserTripDetails', 'API\v1\ApiController@getUserTripDetails');
        Route::post('getWhistleblowerStatusLookup', 'API\v1\ApiController@getWhistleblowerStatusLookup');
        Route::post('getUserBonus', 'API\v1\BonusController@getUserBonusData');

        //video post
        Route::post('getVideoPosts', 'API\v1\ApiController@getVideoPosts');
        Route::post('getVideoUrl', 'API\v1\ApiController@getVideoUrl');

        //Uniform Product Details

        Route::post('getUniformData', 'API\v1\ApiController@getUniformData');
        Route::post('purchaseUniform', 'API\v1\ApiController@purchaseUniform');
        Route::post('uraBalanceInfo', 'API\v1\ApiController@uraBalanceInfo');

        Route::post('getIncidentReports', 'API\v1\ApiController@getIncidentReports');
        Route::post('getIRStatus', 'API\v1\ApiController@getIRStatus');
        Route::post('submitAmendment', 'API\v1\ApiController@submitAmendment');

        Route::post('storeCompilanceAcknowledgment', 'API\v1\ApiController@storeCompilanceAcknowledgment');


        Route::post('getTimesheetSummary', 'API\v1\ApiController@getTimesheetSummary');

        //Chat Module
        Route::post('getAllChat', 'API\v1\ChatMessageController@getAllChat');
        Route::post('getPersonalChat', 'API\v1\ChatMessageController@getPersonalChat');


    });
});

Route::group(['namespace' => 'Modules\Timetracker\Http\Controllers\API\v2', 'prefix' => 'v2'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('app/health', 'ApplicationController@applicationHealth');
    //Only logged in users can access these routes
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('me', 'AuthController@me');
        Route::post('logout', 'AuthController@logout');

        //Routes for getting Customers vistor log template
        Route::get('customers/{customer_id}/templates', 'VisitorLogApiController@getCustomerTemplates');
        Route::get('customers/{customer_id}/templates/{template_id}', 'VisitorLogApiController@getTemplate');
        Route::get('customers/visitor-types', 'VisitorLogApiController@getVisitorTypes');
        Route::get('customers/visitors', 'VisitorLogApiController@fetchVisitors');
        Route::get('customers/{customer_id}/visitors', 'VisitorLogApiController@fetchVisitorsFallback'); //TODO:remove later
        Route::get('customers-allocated', 'VisitorLogApiController@getCustomers');
        Route::post('vlogs/store', 'VisitorLogApiController@storeVisitorLogs');
        Route::post('visitors/store', 'VisitorLogApiController@storeVisitors');
        Route::get('customers/{customer_id}/terms-and-conditions', 'VisitorLogApiController@getTermsAndCondition');
        Route::get('vlogs', 'VisitorLogApiController@getPeerSyncVisitorLogs');

        // Visitor screening questions
        Route::get('customers/{customer_id}/screening-questions', 'VisitorLogApiController@fetchScreeningQuestions');
        Route::post('screening/store', 'VisitorLogApiController@storeScreeningQuestion');
    });
});
