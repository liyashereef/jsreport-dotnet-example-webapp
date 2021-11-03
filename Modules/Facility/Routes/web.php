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

Route::prefix('facility')
    ->name('facility.')
    ->namespace('FacilityUser')
    ->middleware(['web'])
    ->group(function()
    {
        Route::get('/', 'FacilityUserController@index');
        Route::get('/login', 'FacilityUserController@index')->name('login')->middleware('guest:facilityuser');
        Route::post('/login', 'FacilityUserController@login');
        Route::group(['middleware' => ['auth:facilityuser']],function (){
            Route::post('/logout',  array('as' => 'logout', 'uses' => 'FacilityUserController@logout'));
            Route::get('/booking', array('as' => 'booking-page', 'uses' => 'FacilityBookingController@index'));
            Route::get('/alocated-services', array('as' => 'alocated-services', 'uses' => 'FacilityBookingController@getAllocatedServices'));
            Route::get('/booking-data', array('as' => 'booking-data', 'uses' => 'FacilityBookingController@bookingData'));
            Route::post('/book-facility', array('as' => 'book-facility', 'uses' => 'FacilityBookingController@bookFacility'));
            Route::get('/profile-page',  array('as' => 'profile-page', 'uses' => 'FacilityUserController@getProfilePage'));
            Route::get('/logged/user/profile',  array('as' => 'logged.user.profile', 'uses' => 'FacilityUserController@getProfile'));
            Route::post('/profile/update',  array('as' => 'profile-update', 'uses' => 'FacilityUserController@updateProfile'));
            Route::post('/profile/password/reset',  array('as' => 'profile-password-reset', 'uses' => 'FacilityUserController@resetPassword'));
            Route::get('/booking-history',  array('as' => 'user.booking-history', 'uses' => 'FacilityUserController@getBookingHistory'));
        });
    });

Route::group(['middleware' => ['web','auth'], 'prefix' => 'cbs',], function()
{

    Route::post('cbs/populateuserdata',array('as' => 'cbs.populateuserdata', 'uses' => 'FacilityController@populateuserdata'));
    Route::post('cbs/scheduleblock',array('as' => 'cbs.scheduleblock', 'uses' => 'FacilityController@scheduleblock'));
    Route::post('populateuserdata',array('as' => 'cbs.populateusersamenitycategory', 'uses' => 'FacilityController@populateusersamenitycategory'));
    Route::post('cbs/savecondobooking',array('as' => 'cbs.savecondobooking', 'uses' => 'FacilityController@savecondobooking'));
    Route::post('cbs/getbookingstatus',array('as' => 'cbs.bookingstatus', 'uses' => 'FacilityController@getBookingstatus'));

    Route::get('facility',array('as' => 'cbs.facilities', 'uses' => 'FacilityController@viewFacilities'));
    Route::get('editfacilities/{id}',array('as' => 'cbs.editfacilities', 'uses' => 'FacilityController@editfacilities'));
    Route::post('removefacility',array('as' => 'cbs.removefacility', 'uses' => 'FacilityController@removeFacility'));
    Route::get('addfacility',array('as' => 'cbs.addfacility', 'uses' => 'FacilityController@addFacility'));
    Route::post('savefacilitysignout',array('as' => 'cbs.savefacilitysignout', 'uses' => 'FacilityController@saveFacilitysignout'));
    Route::post('editfacilitysignout',array('as' => 'cbs.editfacilitysignout', 'uses' => 'FacilityController@editFacilitysignout'));

    Route::get('manageservice/{id}',array('as' => 'cbs.facilityservice', 'uses' => 'FacilityController@viewFacilityservice'));
    Route::get('addfacilityservice',array('as' => 'cbs.addfacilityservice', 'uses' => 'FacilityController@addFacilityservice'));
    Route::post('savefacilityservice',array('as' => 'cbs.savefacilityservice', 'uses' => 'FacilityController@saveFacilityservice'));
    Route::post('savefacilityservicetiming',array('as' => 'cbs.savefacilityservicetiming', 'uses' => 'FacilityController@saveFacilityservicetiming'));
    Route::post('removeservicetiming',array('as' => 'cbs.removeservicetiming', 'uses' => 'FacilityController@removeServicetiming'));
    Route::get('editfacilityservices/{id}',array('as' => 'cbs.editfacilityservices', 'uses' => 'FacilityController@editFacilityservice'));
    Route::post('removefacilityservices/{id}',array('as' => 'cbs.removefacilityservices', 'uses' => 'FacilityController@removeFacilityservice'));
    Route::post('updatefacilityservice',array('as' => 'cbs.updatefacilityservice', 'uses' => 'FacilityController@updateFacilityservice'));

    Route::post('getuserprerequisites',array('as' => 'cbs.getUserprerequisites', 'uses' => 'FacilityUserController@getUserprerequisites'));
    Route::post('savefacilityprerequisite',array('as' => 'cbs.savefacilityprerequisite', 'uses' => 'FacilityController@saveFacilityprerequisite'));
    Route::post('removefacilityprerequisite',array('as' => 'cbs.removefacilityprerequisite', 'uses' => 'FacilityController@removeFacilityprerequisite'));
    Route::post('savefacilityuserprerequisite',array('as' => 'cbs.savefacilityuserprerequisite', 'uses' => 'FacilityUserController@saveFacilityuserprerequisite'));

    Route::post('savefacilitypolicy',array('as' => 'cbs.savefacilitypolicy', 'uses' => 'FacilityController@saveFacilitypolicy'));
    Route::post('removefacilitypolicy',array('as' => 'cbs.removefacilitypolicy', 'uses' => 'FacilityController@removeFacilitypolicy'));

    Route::get('facilityusers',array('as' => 'cbs.facilityusers', 'uses' => 'FacilityUserController@viewFacilityusers'));
    Route::get('facilityuserallocations',array('as' => 'cbs.facilityuserallocations', 'uses' => 'FacilityUserController@viewFacilityuserallocation'));
    Route::post('addfacilityuser',array('as' => 'cbs.addfacilityuser', 'uses' => 'FacilityUserController@addFacilityusers'));
    Route::post('editfacilityuser',array('as' => 'cbs.editfacilityuser', 'uses' => 'FacilityUserController@editFacilityusers'));
    Route::post('getuserdetails',array('as' => 'cbs.facilityuserdetails', 'uses' => 'FacilityUserController@getUserdetails'));
    Route::post('removefacilityuser',array('as' => 'cbs.removefacilityuser', 'uses' => 'FacilityUserController@removeFacilityusers'));
    Route::post('allocatefacilityuser',array('as' => 'cbs.allocatefacilityuser', 'uses' => 'FacilityUserController@addAllocatefacilityusers'));
    Route::post('saveorremoveallocation',array('as' => 'cbs.saveorremoveallocation', 'uses' => 'FacilityUserController@saveorremoveallocation'));
    Route::post('saveorremovemassallocation',array('as' => 'cbs.saveorremovemassallocation', 'uses' => 'FacilityUserController@saveorremovemassallocation'));
    Route::post('saveorremovedayallocation',array('as' => 'cbs.saveorremovedayallocation', 'uses' => 'FacilityUserController@saveorremovedayallocation'));
    Route::post('savefacilityservicelockdown',array('as' => 'cbs.savefacilityservicelockdown', 'uses' => 'FacilityController@saveFacilityservicelockdown'));
    Route::post('removefacilityservicelockdown',array('as' => 'cbs.removefacilityservicelockdown', 'uses' => 'FacilityController@removeFacilityservicelockdown'));

    Route::post('customersfacility',array('as' => 'cbs.customersfacility', 'uses' => 'FacilityUserController@getCustomersfacility'));
    Route::get('/booking',array('as' => 'cbs.booking-page', 'uses' => 'BookingManagementController@index'));
    Route::get('/facility/service',array('as' => 'cbs.booking.facility-service', 'uses' => 'BookingManagementController@getFacilityServices'));
    Route::get('/booking-data',array('as' => 'cbs.booking-data', 'uses' => 'BookingManagementController@bookingData'));
    Route::delete('/booking-data/delete',array('as' => 'cbs.booking-data.delete', 'uses' => 'BookingManagementController@bookingDataRemovel'));
});
