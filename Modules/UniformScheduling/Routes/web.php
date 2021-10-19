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

Route::group(['middleware' => ['web'], 'prefix' => 'uniform',], function(){
    Route::get('/', 'UserLoginController@index');
    Route::get('/login', 'UserLoginController@index')->name('uniform.login');
    Route::post('/login', 'UserLoginController@login');
    Route::get('/booking', array('as' => 'uniform.booking-page', 'uses' => 'UniformSchedulingBookingController@index'));
});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'uniform',], function(){
    Route::post('/logout',  array('as' => 'uniform.logout', 'uses' => 'UserLoginController@logout'));
    Route::get('/booking-data', array('as' => 'uniform.booking-data', 'uses' => 'UniformSchedulingBookingController@bookingData'));
    Route::post('/booking', array('as' => 'uniform.book-slot', 'uses' => 'UniformSchedulingBookingController@slotBooking'));
});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'uniform-admin', 'namespace' => 'Admin'], function(){
    // Booking management
    Route::get('/',array('as' => 'uniform-admin', 'uses' => 'UniformSchedulingEntriesController@index'));
    Route::get('/slot-timings',array('as' => 'uniform-admin.slot-timings-booking', 'uses' => 'UniformSchedulingEntriesController@getOfficeSlotTimings'));
    Route::get('/slot-booking/{id}',array('as' => 'uniform-admin.slot-single-booking', 'uses' => 'UniformSchedulingEntriesController@getBookingEntryById'));
    Route::post('/slot-booking/update',array('as' => 'uniform-admin.booking.update', 'uses' => 'UniformSchedulingEntriesController@updateBooking'));
    Route::post('/slot-booking/delete',array('as' => 'uniform-admin.booking.delete', 'uses' => 'UniformSchedulingEntriesController@deleteBooking'));
    Route::get('/office-free/slots',array('as' => 'uniform-admin.office-free.slots', 'uses' => 'UniformSchedulingEntriesController@getOfficeFreeSlot'));
    // Booking list
    Route::get('/reports/appoinments',array('as' => 'uniform-admin.slot-booking.list-page', 'uses' => 'UniformSchedulingEntriesController@getBookingListPage'));
    Route::get('/reports/appoinment-data',array('as' => 'uniform-admin.slot-booking.lists', 'uses' => 'UniformSchedulingEntriesController@getBookingLists'));


});

