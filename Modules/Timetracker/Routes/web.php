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
//normal routes
Route::group([
    'middleware' => ['web', 'auth', 'permission:view_timetracker'],
    'prefix' => 'timetracker',
    
], function () {
    Route::get('/', 'TimetrackerController@index');
    Route::get('vlogs', 'API\v2\VisitorLogApiController@getPeerSyncVisitorLogs');

    Route::get('/widgetDataEntry/{start_date?}/{end_date?}', 'TimetrackerController@widgetDataEntry');

    Route::get('employeeTimeoff', array('as' => 'timeoff.index', 'uses' => 'EmployeeTimeoffController@index'));
    Route::get('getTimeoffRequests', array('as' => 'timeoff.timeoff_requests', 'uses' => 'EmployeeTimeoffController@getTimeoffRequests'));
    Route::get('timeoffrequestform', array('as' => 'timeoff.timeoffRequesForm', 'uses' => 'EmployeeTimeoffController@showTimeoffRequestForm'));
    Route::post('storeTimeoffRequestForm', array('as' => 'timeoff.timeoffRequestFormstore', 'uses' => 'EmployeeTimeoffController@storeTimeoffRequestForm'));
    Route::get('payRollList/{project_id?}', array('as' => 'timeoff.timeoffPayRoll', 'uses' => 'EmployeeTimeoffController@payRollList'));
    Route::get('backFillprocess/customer/{customer_id?}/{requirement_id?}', array('as' => 'timeoff.timeoffBackFill', 'uses' => 'EmployeeTimeoffController@backFillprocess'));

    Route::middleware(['permission:view_timesheet_by_employee'])->group(function () {
        Route::get('timesheet', array(
            'as' => 'timetracker.timesheet',
            'uses' => 'TimetrackerController@reportTimeSheet'
        ));
        Route::get('getTimesheetReport', array(
            'as' => 'timetracker.getTimesheetReport',
            'uses' => 'TimetrackerController@getTimesheetReport'
        ));
    });

    Route::middleware(['permission:view_timesheet_detail_view'])->group(function () {
        Route::get('timesheetdetail', array(
            'as' => 'timetracker.timesheet-detail', 'uses' => 'TimetrackerController@reportTimeSheetDetail'
        ));
        Route::get('getTimesheetReportDetail', array(
            'as' => 'timetracker.getTimesheetReportDetail',
            'uses' => 'TimetrackerController@getTimesheetReportDetail'
        ));
    });

    Route::middleware(['permission:view_employee_summary'])->group(function () {
        Route::get('employee-summary', array(
            'as' => 'timetracker.employee-summary',
            'uses' => 'TimetrackerController@employeeSummary'
        ));
        Route::get('getEmployeeSummaryReport', array(
            'as' => 'timetracker.getEmployeeSummaryReport',
            'uses' => 'TimetrackerController@getEmployeeSummaryReport'
        ));
    });

    Route::get('employee-performance', array(
        'as' => 'timetracker.employee-performance',
        'uses' => 'TimetrackerController@employeePerformance'
    ));
    Route::get('getEmployeePerformanceReport', array(
        'as' => 'timetracker.getEmployeePerformanceReport',
        'uses' => 'TimetrackerController@getEmployeePerformanceReport'
    ));

    Route::middleware(['permission:view_allocation_report'])->group(function () {
        Route::get('allocation', array(
            'as' => 'timetracker.allocation',
            'uses' => 'TimetrackerController@allocation'
        ));
        Route::get('getAllocationReport', array(
            'as' => 'timetracker.getAllocationReport',
            'uses' => 'TimetrackerController@getAllocationReport'
        ));
    });

    /* Notification - Start */
    Route::middleware(['permission:view_notification'])->group(function () {
        Route::get('notification', array(
            'as' => 'notification.index', 'uses' => 'NotificationController@index'
        ));
        Route::post('notification/read', array(
            'as' => 'notification.read', 'uses' => 'NotificationController@read'
        ));
        Route::post('notification/delete', array(
            'as' => 'notification.delete', 'uses' => 'NotificationController@delete'
        ));
        Route::post('notification/multiDelete', array(
            'as' => 'notification.multiDelete', 'uses' => 'NotificationController@multiDelete'
        ));
        Route::get('notification/getNotificationMessage', array(
            'as' => 'notification.getNotificationMessage',
            'uses' => 'NotificationController@getNotificationMessage'
        ));
        Route::resource('notification', 'NotificationController', ['only' => [
            'index', 'destroy', 'view', 'getNotificationMessage', 'delete', 'read',
        ]]);
    });

    /* Notification -End */

    /* Timesheet Approval - Start */
    Route::middleware(['permission:view_timesheet_approval'])->group(function () {
        Route::post(
            'approval/store',
            array(
                'as' => 'approval.store',
                'uses' => 'TimesheetApprovalController@store'
            )
        );
        Route::get('approval/timesheet', array(
            'as' => 'approval.timesheet', 'uses' => 'TimesheetApprovalController@timesheet'
        ));
        Route::get('approval/timesheet/view/{id}', array(
            'as' => 'approval.view', 'uses' => 'TimesheetApprovalController@detailedview'
        ));
        // ->middleware('is-current-supervisor');
        //Route::post('timesheet/store', array('as' => 'timesheet.store', 'uses' => 'Supervisor\TimesheetApprovalController@store'));
        Route::get('approval/getTimesheetReport', array(
            'as' => 'approval.getTimesheetReport', 'uses' => 'TimesheetApprovalController@getTimesheet'
        ));
        Route::post('approval/gettimesheetcpiddata', array(
            'as' => 'approval.getTimesheetCpidData', 'uses' => 'TimesheetApprovalController@getTimesheetCpidData'
        ));
        Route::resource('approval', 'TimesheetApprovalController', ['only' => [
            'timesheet', 'getTimesheet', 'store', 'detailedview',
        ]]);
        Route::get('approval/timesheet-export', array(
            'as' => 'timesheet.export-approved',
            'uses' => 'TimesheetApprovalController@approvedTimesheetExport'
        ));
        Route::get('approval/timesheet-export-vision', array(
            'as' => 'timesheet.export-approved-vision',
            'uses' => 'TimesheetApprovalController@approvedTimesheetExportVision'
        ));
        Route::get('approval/rating', array('as' => 'timesheet.rating', 'uses' => 'TimesheetApprovalController@employeeTimeSheetApprovalRating'));
        Route::get('approval/notification', array('as' => 'timesheet.notification', 'uses' => 'TimesheetApprovalController@employeeTimesheetApprovalEmailNotification'));
    });
    /* Timesheet Approval - End */

    Route::middleware(['permission:view_all_mobile_security_patrol|view_allocated_mobile_security_patrol'])->group(function () {
        Route::get('mobilesecuritypatrol/trips', array(
            'as' => 'mobilesecuritypatrol.trips', 'uses' => 'MobileSecurityPatrolController@index'
        ));

        Route::get('mobilesecuritypatrol/trips/list', array(
            'as' => 'mobilesecuritypatrol.list', 'uses' => 'MobileSecurityPatrolController@list'
        ));
        Route::get('mobilesecuritypatrol/trips/mapview/{trip_id}', array(
            'as' => 'mobilesecuritypatrol.mapview', 'uses' => 'MobileSecurityPatrolController@mapView'
        ));
        Route::get('mobilesecuritypatrol/trips/tripdetails/{trip_id}', array(
            'as' => 'mobilesecuritypatrol.tripdetailsview',
            'uses' => 'MobileSecurityPatrolController@tripDetailsView'
        ));
        Route::get('mobilepatrol', array(
            'as' => 'mobilepatrol', 'uses' => 'MobileSecurityPatrolController@mobilepatrol'
        ));
        Route::get('mobilepatrol/list', array(
            'as' => 'mobilepatrol.list', 'uses' => 'MobileSecurityPatrolController@mobilepatrollist'
        ));
        Route::get('mobilesecuritypatrol/trips/tripmapview/{trip_id}', array(
            'as' => 'mobilesecuritypatrol.tripmapview',
            'uses' => 'MobileSecurityPatrolController@fullshift_mapView'
        ));
    });
    Route::middleware(['permission:view_all_satellite_tracking|view_allocated_satellite_tracking'])->group(function () {
        Route::get(
            'mobilesecuritypatrol/geofence',
            [
                'as' => 'msp.geofence.view',
                'uses' => 'MobileSecurityPatrolController@geofence'
            ]
        );

        Route::get(
            'mobilesecuritypatrol/geofence-list',
            [
                'as' => 'msp.geofence.list',
                'uses' => 'MobileSecurityPatrolController@geofenceList'
            ]
        );

        Route::get('mobilesecuritypatrol/geofence-summary/{shift_id}', array(
            'as' => 'msp.geofence.summary', 'uses' => 'MobileSecurityPatrolController@getGeoSummary'
        ));

        Route::get(
            'mobilesecuritypatrol/geofencedatalist',
            [
                'as' => 'msp.geofence.datalist',
                'uses' => 'MobileSecurityPatrolController@geofenceDataList'
            ]
        );

        Route::get(
            'mobilesecuritypatrol/geofence/customer-summary',
            [
                'as' => 'msp.geofence.customer.summary',
                'uses' => 'MobileSecurityPatrolController@geofenceCustomerSummary'
            ]
        );

        //todo::remove
        Route::get(
            'mobilesecuritypatrol/geofence/dashboard/satellite-tracking',
            [
                'as' => 'msp.geofence.dashboard.satellite-tracking',
                'uses' => 'MobileSecurityPatrolController@geofenceCustomerSummaryDesign'
            ]
        );

        Route::get(
            'mobilesecuritypatrol/geofence/sat-dashboard-mapchildrows',
            [
                'as' => 'msp.geofence.sat-dashboard-mapchildrows',
                'uses' => 'MobileSecurityPatrolController@getSatelliteTrackingDashboardMapDatachildrows'
            ]
        );

        Route::get(
            'mobilesecuritypatrol/geofence/sat-dashboard-map',
            [
                'as' => 'msp.geofence.sat-dashboard-map',
                'uses' => 'MobileSecurityPatrolController@getSatelliteTrackingDashboardMapData'
            ]
        );

        Route::get(
            'mobilesecuritypatrol/geofence/sat-dashboard-table',
            [
                'as' => 'msp.geofence.sat-dashboard-table',
                'uses' => 'MobileSecurityPatrolController@getSatelliteTrackingDashboardTableData'
            ]
        );
    });

    Route::get('qrcodepatrol/location/list', array('as' => 'qrcodepatrol.list', 'uses' => 'QrcodePatrolController@list'));
    Route::get('qrcodepatrol/location', array('as' => 'qrcodepatrol.trips', 'uses' => 'QrcodePatrolController@index'));
    Route::get('qrcodepatrol/location/mapview/{trip_id}', array('as' => 'qrcodepatrol.mapview', 'uses' => 'QrcodePatrolController@qrcodeMapView'));
    Route::get('qrcodepatrol/location/details/{shift_id}', array('as' => 'qrcodepatrol.details', 'uses' => 'QrcodePatrolController@getQrpatrolDetails'));
    Route::get('customerqrcodeshift/summary', array('as' => 'customerqrcodeshift.summary', 'uses' => 'CustomerQrcodeShiftController@index'));
    Route::get('customerqrcodeshift/summary/list/{startdate?}/{enddate?}/{emp_id?}/{client_id?}', array('as' => 'customerqrcodeshift.list', 'uses' => 'CustomerQrcodeShiftController@getList'));
//    Route::get('qrcodepatrol/dailyActivityReport', array('as' => 'qrcodepatrol.dailyActivityReport', 'uses' => 'QrcodePatrolController@qrPatroldailyActivity'));
    /** -----START---- MST ---------------------------- */

    /** Dispatch Request */

    Route::get('dispatch_request/list', array(
        'as' => 'dispatch_request.list',
        'uses' => 'DispatchRequestController@list'
    ));
    Route::get('dispatch_request/decline_list/{id}', array(
        'as' => 'dispatch_request.decline_list',
        'uses' => 'DispatchRequestController@declineList'
    ));
    Route::get('dispatchrequest/customer_details/{id}', array(
        'as' => 'dispatch_request.customer_details',
        'uses' => 'DispatchRequestController@getCustomerDetails'
    ));
    Route::get('dispatchrequest/request_type/{id}', array(
        'as' => 'dispatch_request.request_type',
        'uses' => 'DispatchRequestController@getRequestTypeDetails'
    ));
    Route::get('dispatchrequest/loacation/byPostalCode/{postalcode}', array(
        'as' => 'dispatch_request.location_by_postal_code',
        'uses' => 'DispatchRequestController@getLoacationByPostalCode'
    ));
    Route::get('dispatchrequest/triggerPushNotification/{dispatch_request_id}', array(
        'as' => 'dispatch_request.triggerPushNotification',
        'uses' => 'DispatchRequestController@triggerPushNotification'
    ));
    Route::get('dispatchrequest/statusclose/{id}', array(
        'as' => 'dispatchrequest.statusclose',
        'uses' => 'DispatchRequestController@statusClose'
    ));
    //Dispatch Request resource controller
    Route::resource('dispatchrequest', 'DispatchRequestController');

    //Get dispatch requests by status string (input as csv)
    Route::get(
        'dispatch_request/status_array',
        'API\v1\DispatchRequestApiController@getByStatusStrings'
    )
        ->name('dispatch_coordinates_status_array_web');

    //dashboard
    Route::get('mst_dispatch/dashboard', array(
        'as' => 'mst_dispatch.dashboard',
        'uses' => 'MSTDispatchDashboardController@index'
    ));

    Route::get('getMSTRelatedRoles', 'MSTDispatchDashboardController@getMSTRelatedRoles');
    Route::get('toGetAllocatedUsers', 'MSTDispatchDashboardController@toGetAllocatedUsers');

    /** ----------END------ MST ---------------------------- */



    //masters section
    Route::group([
        'middleware' => ['web', 'auth', 'permission:view_admin'],
        'prefix' => 'admin',
        'namespace' => 'Admin'
    ], function () {
        /* Templates Settings - start */
        Route::get('satellite-tracking-settings', [
            'as' => 'satellite-tracking-settings.list',
            'uses' => 'SatelliteTrackingSettingsController@index'
        ]);
        Route::post('satellite-tracking-settings', [
            'as' => 'satellite-tracking-settings.save',
            'uses' => 'SatelliteTrackingSettingsController@save'
        ]);
    });


    /** ----------START------ Live Locations ---------------------------- */

    /** Reguler shift Device Coordinates */
    Route::middleware(['permission:view_all_live_location|view_allocated_live_location|admin|super_admin'])->group(function () {

        Route::get('employee_shift_coordinates', 'LiveLocationController@getEmployeeLiveCoodinates')
            ->name('employee_shift_coordinates');

        Route::get('shift-live-locations/{shift_type?}', array(
            'as' => 'timetracker.shift-live-locations',
            'uses' => 'LiveLocationController@liveShiftLocations'
        ));
        /** mst Device Coordinates */
        Route::get('dispatch_request_coordinates', 'LiveLocationController@getEmployeeLiveCoodinates')
            ->name('dispatch_request_coordinates_web');

        Route::get('active_shift_employees/{shift_type?}/{customer_id?}', 'LiveLocationController@listAllActiveShiftEmployees')
            ->name('active_shift_employees');
    });



    /** ----------END------ Live Locations ---------------------------- */

    /* Manual Timesheet Entry - Start */
    Route::group(['middleware' => ['permission:view_manual_timesheet_entry']], function () {
        Route::get('manual-timesheet-entry', array('as' => 'timetracker.manualtimesheetentry', 'uses' => 'ManualTimesheetEntryController@manualTimesheetEntry'));
        Route::get('manual-timesheet-entry/employee-list/{customer_id}', array('as' => 'timetracker.manualtimesheetentry.employeelist', 'uses' => 'ManualTimesheetEntryController@getCustomerEmployeeAllocationList'));
        Route::get('manual-timesheet-entry/activity-code-list/{customer_id}/{work_hour_type_id}', array('as' => 'timetracker.manualtimesheetentry.activitycodelist', 'uses' => 'ManualTimesheetEntryController@getActivityCodeList'));
        Route::get('manual-timesheet-entry/rate/{cpid}', array('as' => 'timetracker.manualtimesheetentry.rate', 'uses' => 'ManualTimesheetEntryController@getRate'));
        Route::post('manual-timesheet-entry/store', array('as' => 'timetracker.manualtimesheetentry.store', 'uses' => 'ManualTimesheetEntryController@store'));
        Route::get('manual-timesheet-entry/employee-check/{payperiod}/{week}/{user}', array('as' => 'timetracker.manualtimesheetentry.employeecheck', 'uses' => 'ManualTimesheetEntryController@employeecheck'));
    });
    /* Manual Timesheet Entry - Stop */

    /* Manual Timesheet Report - Start */
    Route::group(['middleware' => ['permission:view_manual_timesheet_report']], function () {
        Route::get('manual-timesheet-report', array('as' => 'timetracker.manualtimesheetreport', 'uses' => 'ManualTimesheetReportController@ManualTimesheetReport'));
        Route::get('manual-timesheet-report/list', array('as' => 'timetracker.manualtimesheetreport.list', 'uses' => 'ManualTimesheetReportController@getList'));
        Route::get('manual-timesheet-report/edit/{id}', array('as' => 'timetracker.manualtimesheetreport.edit', 'uses' => 'ManualTimesheetReportController@getEditData'));
        Route::post('manual-timesheet-report/trash', array('as' => 'timetracker.manualtimesheetreport.trash', 'uses' => 'ManualTimesheetReportController@trashManualData'));
        Route::get('manual-timesheet-report/edit/customer/{customer_id}', array('as' => 'timetracker.manualtimesheetreport.edit.customer', 'uses' => 'ManualTimesheetReportController@getEditCustomer'));
        Route::get('manual-timesheet-report/activity-code-list/{customer_id}/{work_hour_type_id}', array('as' => 'timetracker.manualtimesheetreport.activitycodelist', 'uses' => 'ManualTimesheetReportController@getActivityCodeList'));
        Route::post('manual-timesheet-report/update', array('as' => 'timetracker.manualtimesheetreport.update', 'uses' => 'ManualTimesheetReportController@updateEntry'));
    });
    /* Manual Timesheet Report - Stop */
});
