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

Route::get('IDS', function () {
    return redirect()->route('idsscheduling');
});
Route::group(['prefix' => 'ids', 'namespace' => 'Modules\IdsScheduling\Http\Controllers'], function(){
    Route::get('/',array('as' => 'idsscheduling', 'uses' => 'IdsSlotBookingController@index'));
    Route::post('/office/slot/booking',array('as' => 'ids-office.slot-booking', 'uses' => 'IdsSlotBookingController@slotBooking'));
    Route::post('/booking/fee-calculation',array('as' => 'ids-office.slot-booking.fee-calculation', 'uses' => 'IdsSlotBookingController@feeCalculation'));
    Route::get('/last-cancelled/entry',array('as' => 'ids.last-cancelled-entry', 'uses' => 'IdsSlotBookingController@lastCancelledEntry'));
    Route::get('/offices',array('as' => 'ids-offices', 'uses' => 'IdsSlotBookingController@getAllOffice'));
    Route::get('/office/services/{officeId}',array('as' => 'ids-office-services', 'uses' => 'IdsSlotBookingController@getOfficeService'));
    Route::get('/office/slot-details',array('as' => 'ids-office.slot-details', 'uses' => 'IdsSlotBookingController@getOfficeSlotDetails'));
    Route::get('/office/pincode-recommendation',array('as' => 'ids-office.pincode-recommendation', 'uses' => 'IdsSlotBookingController@getRecommendOfficeByPincode'));
    Route::get('/slot-area',array('as' => 'idsscheduling.slot-area', 'uses' => 'IdsSlotBookingController@slotArea'));
    Route::get('/office/slot-timings',array('as' => 'ids-office.slot-timings', 'uses' => 'IdsSlotBookingController@getOfficeSlotTimings'));
    Route::post('/paynow',array('as' => 'ids.paynow', 'uses' => 'IdsPaymentController@index'));
    Route::get('/paymentSuccess',array('as' => 'ids.paymentSuccess', 'uses' => 'IdsPaymentController@bookingPaymentSuccess'));
});
 
Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'idsscheduling-admin', 'namespace' => 'Admin'], function(){

    Route::middleware(['permission:ids_view_all_schedule|ids_view_allocated_locaion_schedule'])->group(function () {
        Route::get('/',array('as' => 'idsscheduling-admin', 'uses' => 'IdsSchedulingController@index'));
        Route::get('/office/slot-details',array('as' => 'idsscheduling-admin.office.slot-details', 'uses' => 'IdsSchedulingController@getOfficeSlots'));
        Route::get('/office/slot-timings',array('as' => 'idsscheduling-admin.office.slot-timings', 'uses' => 'IdsSchedulingController@getOfficeSlotTimings'));
        Route::get('/office/slot-single-booking',array('as' => 'idsscheduling-admin.office.slot-single-booking', 'uses' => 'IdsSchedulingController@getBookingEntryById'));
        Route::post('/office/slot-booking/update',array('as' => 'idsscheduling-admin.office.slot-update', 'uses' => 'IdsSchedulingController@updateSlotBooking'));
        Route::post('/office/slot-booking/delete',array('as' => 'idsscheduling-admin.office.slot-delete', 'uses' => 'IdsSchedulingController@deleteSlotBooking'));
        Route::get('/office/free-slot',array('as' => 'idsscheduling-admin.office.free-slot', 'uses' => 'IdsSchedulingController@getOfficeFreeSlot'));
        Route::post('/office/slot-booking/toBeReshedule',array('as' => 'idsscheduling-admin.office.to-be-rescheduling', 'uses' => 'IdsSchedulingController@setToBeReshedule'));

        Route::get('calendar',array('as' => 'idsscheduling-calendar-admin', 'uses' => 'IdsSchedulingController@getCalendar'));
        Route::get('calendar/office/booking-details',array('as' => 'idsscheduling-calendar.office.booking', 'uses' => 'IdsSchedulingController@getCalendarData'));
        Route::get('/calendar/office/slot-details',array('as' => 'idsscheduling-calendar.office.slot-details', 'uses' => 'IdsSchedulingController@getDaySlotDetails'));

        Route::get('cancelled/schedules',array('as' => 'idsscheduling-admin.cancelled-schedule', 'uses' => 'IdsSchedulingController@getCancelledSchedule'));
        Route::get('cancelled/schedules/data',array('as' => 'idsscheduling-admin.cancelled-schedule.data', 'uses' => 'IdsSchedulingController@getCancelledScheduleData'));
    });

    Route::middleware(['permission:ids_view_report'])->group(function () {
        Route::get('/report',array('as' => 'idsscheduling-admin.report', 'uses' => 'ReportController@getForecastPage'));
        Route::get('/service-reports',array('as' => 'idsscheduling-admin.office.service-reports', 'uses' => 'ReportController@getServiceReport'));

        Route::get('/analytics',array('as' => 'idsscheduling-admin.analytics', 'uses' => 'ReportController@getAnalyticsPage'));
        Route::get('/analytics-reports',array('as' => 'idsscheduling-admin.office.analytics-reports', 'uses' => 'ReportController@getAnalyticsReport'));


        Route::get('/trends',array('as' => 'idsscheduling-admin.trends', 'uses' => 'ReportController@getTrendsPage'));
        Route::get('/trends-reports',array('as' => 'idsscheduling-admin.office.trends-reports', 'uses' => 'ReportController@getTrendsReport'));

        Route::get('/revenue-reports',array('as' => 'idsscheduling-admin.revenue', 'uses' => 'ReportController@getRevenuePage'));
        Route::get('/revenue',array('as' => 'idsscheduling-admin.office.revenue-reports', 'uses' => 'ReportController@getRevenueReport'));

        Route::get('/geomap-reports',array('as' => 'idsscheduling-admin.geomap', 'uses' => 'ReportController@getAppointmentGeoMap'));
        Route::get('/geomap',array('as' => 'idsscheduling-admin.office.geomap-reports', 'uses' => 'ReportController@getAppointmentGeoMapData'));

        Route::get('/photo-revenue-reports',array('as' => 'idsscheduling-admin.photo-revenue', 'uses' => 'ReportController@getPhotoRevenuePage'));
        Route::get('/photo-revenue',array('as' => 'idsscheduling-admin.office.photo-revenue-reports', 'uses' => 'ReportController@getPhotoRevenueReport'));


        Route::get('/revenue-office-reports',array('as' => 'idsscheduling-admin.office-revenue', 'uses' => 'ReportController@getOfficeRevenuePage'));
        Route::get('/revenue-office',array('as' => 'idsscheduling-admin.office-revenue-reports', 'uses' => 'ReportController@getOfficeRevenueReport'));

    });

    Route::middleware(['permission:ids_refund_list|ids_refund_update_status'])->group(function () {
        Route::get('/refund-list',array('as' => 'idsscheduling-admin.refund', 'uses' => 'ReportController@getRefundPage'));
        Route::get('/refund',array('as' => 'idsscheduling-admin.office.refund-list', 'uses' => 'ReportController@getRefundReport'));
        Route::post('/refund-confirm',array('as' => 'idsscheduling-admin.office.refund-confirm', 'uses' => 'IdsSchedulingController@getRefundConfirm'));
        Route::get('/office/slot-single-booking-trashed',array('as' => 'idsscheduling-admin.office.slot-single-booking-trashed', 'uses' => 'IdsSchedulingController@getEntryByIdWithTrashed'));
    });

});
