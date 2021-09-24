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

Route::group(['middleware' => ['web', 'auth']], function () {
    /* Profile updation - Start */
    Route::get('profile/edit', array('as' => 'profile.edit', 'uses' => 'Modules\Admin\Http\Controllers\UserController@view'));
    Route::post('profile/updateProfile/{id}', array('as' => 'profile.updateProfile', 'uses' => 'Modules\Admin\Http\Controllers\UserController@updateProfile'));
    /* Profile updation - End */
});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin', 'namespace' => 'Modules\Admin\Http\Controllers'], function () {
    Route::get('payperiod/getCurrentPayPeriod', array('as' => 'payperiod.getCurrentPayPeriod', 'uses' => 'PayPeriodController@getCurrentPayPeriod'));
});

Route::group(['middleware' => ['web', 'auth', 'permission:view_admin'], 'prefix' => 'admin', 'namespace' => '\Modules\Admin\Http\Controllers'], function () {

    Route::name('admin')->get('index', 'AdminController@index');
    Route::name('permission-mapping')->get('permission-mapping', 'PermissionMappingController@index');
    Route::get('permission-mapping/list', array('as' => 'permission-mapping.list', 'uses' => 'PermissionMappingController@getList'));
    Route::post('permission-mapping/store', array('as' => 'permission-mapping.store', 'uses' => 'PermissionMappingController@store'));
    Route::get('permission-mapping/edit/{id}', array('as' => 'permission-mapping.single', 'uses' => 'PermissionMappingController@edit'));
    /* User - start */
    Route::group(['middleware' => ['permission:manage-users']], function () {
        Route::name('user')->get('user', 'UserController@index');
        Route::post('user/import/process', array('as' => 'user.import.process', 'uses' => 'UserController@importFileIntoDB'));
        Route::get('user/list/{active?}', array('as' => 'user.list', 'uses' => 'UserController@getList'));
        Route::post('user/store', array('as' => 'user.store', 'uses' => 'UserController@store'));
        Route::get('user/show/{id}', array('as' => 'user.show', 'uses' => 'UserController@show'));
        Route::get('user/formattedUserDetails/{id}', array('as' => 'user.formattedUserDetails', 'uses' => 'UserController@formattedUserDetails'));
        Route::post('user/import/process', array('as' => 'user.import.process', 'uses' => 'UserController@userImport'));
        Route::get('user/visionexport', array('as' => 'user.export.vision', 'uses' => 'UserController@userVisionExport'));
    });
    Route::name('users.genericpwd')->get('users/genericpwd', 'UserController@genericpwd');
    /* User - end */

    Route::name('customer-shift-module-dropdown')->get('customer-shift-module-dropdown', 'ShiftModuleDropdownController@index');
    Route::get('shift-module-dropdown/list', array('as' => 'shift-module-dropdown.list', 'uses' => 'ShiftModuleDropdownController@getList'));
    Route::get('shift-module-dropdown/add', array('as' => 'shift-module-dropdown.add', 'uses' => 'ShiftModuleDropdownController@addDropdown'));
    Route::post('shift-module-dropdown/add', array('as' => 'shift-module-dropdown.add', 'uses' => 'ShiftModuleDropdownController@storeDropdown'));
    Route::get('shift-module-dropdown/update/{id}', array('as' => 'shift-module-dropdown.update', 'uses' => 'ShiftModuleDropdownController@addDropdown'));

    /* Customer - start */
    Route::group(['middleware' => ['permission:manage-customers']], function () {
        Route::name('customer')->get('customer', 'CustomerController@index');
        Route::get('customer/list/{customer_type?}/{customer_status?}', array('as' => 'customer.list', 'uses' => 'CustomerController@getList'));
        Route::get('customer/add', array('as' => 'customer.add', 'uses' => 'CustomerController@addCustomer'));
        Route::get('customer/edit/{id}', array('as' => 'customer.edit', 'uses' => 'CustomerController@editCustomer'));
        Route::get('customer/fencelist', array('as' => 'customer.fencelist', 'uses' => 'CustomerController@getFenceList'));
        Route::get('customer/getLocation', array('as' => 'customer.getLocation', 'uses' => 'CustomerController@getAddressLocation'));
        Route::get('customer/fencelistarray', array('as' => 'customer.fencelistarray', 'uses' => 'CustomerController@getFenceListArray'));
        Route::post('customer/editfencelist', array('as' => 'customer.editFence', 'uses' => 'CustomerController@editFence'));
        Route::post('customer/disablefence', array('as' => 'customer.disablefence', 'uses' => 'CustomerController@disablefence'));
        Route::get('customer/removefencelist', array('as' => 'customer.removefencelist', 'uses' => 'CustomerController@removeFenceList'));
        Route::get('customer/namelist/{customer_type?}', array('as' => 'customer.namelist', 'uses' => 'CustomerController@getCustomersNameIdList'));
        Route::get('customer/single/{id}', array('as' => 'customer.single', 'uses' => 'CustomerController@getSingle'));
        Route::post('customer/store', array('as' => 'customer.store', 'uses' => 'CustomerController@store'));
        Route::post('customer/updateLatLong', array('as' => 'customer.updateLatLong', 'uses' => 'CustomerController@updateLatLong'));
        Route::get('customer/formattedProjectDetails/{id}', array('as' => 'customer.formattedProjectDetails', 'uses' => 'CustomerController@formattedProjectDetails'));
        Route::post('customer/import/process', array('as' => 'customer.import.process', 'uses' => 'CustomerController@customerImport'));
        Route::post('customer/reset_incident_logo', array('as' => 'customer.reset_incident_logo', 'uses' => 'CustomerController@resetIncidentLogo'));
        Route::get('color_settings', array('as' => 'colorsettings', 'uses' => 'CustomerController@showColorSettings'));
        Route::get('getColorsettings', array('as' => 'admin.colorsettings', 'uses' => 'CustomerController@getColorSettings'));
        Route::get('getColorsettingssingle/{id}', array('as' => 'admin.colorsettings.single', 'uses' => 'CustomerController@getColorSettingssingle'));
        Route::post('setColorsettings', array('as' => 'admin.colorsettings.store', 'uses' => 'CustomerController@setColorSettings'));
        Route::post('removeColorsettings/{id}', array('as' => 'admin.colorsettings.destroy', 'uses' => 'CustomerController@removeColorSettings'));
        Route::get('customer/allocatteduseremail/{userid?}', array('as' => 'customer.allocatteduseremail', 'uses' => 'CustomerController@getAllocatedUserEmail'));
        // Landing Page Tab Details
        Route::get('customer/getLandingPageDetails', array('as' => 'customer.getLandingPageDetails', 'uses' => 'LandingPageController@getLandingPageDetails'));

        Route::name('customerfences')->get('customerfences', 'CustomerController@customerfences');
        Route::get('guardroutes', array('as' => 'guardroutes', 'uses' => 'CustomerController@guardroutes'));
        Route::get('guardroutesdata', array('as' => 'guardroutesdata', 'uses' => 'CustomerController@guardroutesdata'));
        Route::post('postguardroutes', array('as' => 'postguardroutes', 'uses' => 'CustomerController@postguardroutes'));
        Route::get('guardroutesdatadetails', array('as' => 'routes.details', 'uses' => 'CustomerController@guardroutesdatadetailed'));
        /* Customer - End */

        /* Customer Shift - Start */
        Route::name('customershift')->get('customers-shift', 'CustomerShiftController@index');
        Route::get('customershift/list', array('as' => 'customershift.list', 'uses' => 'CustomerShiftController@getList'));
        Route::post('customershift/store', array('as' => 'customershift.store', 'uses' => 'CustomerShiftController@store'));
        Route::get('customershift/destroy/{id}', array('as' => 'customershift.destroy', 'uses' => 'CustomerShiftController@destroy'));
        Route::get('customershift/single/{id}', array('as' => 'customershift.single', 'uses' => 'CustomerShiftController@getSingle'));
        /* Customer Shift - End */

        /* Parent Customer - Start */
        Route::name('parentcustomer')->get('parentcustomer', 'ParentCustomerController@index');
        Route::get('parentcustomer/list/{customer_type?}', array('as' => 'parentcustomer.list', 'uses' => 'ParentCustomerController@getList'));
        Route::get('parentcustomer/namelist/{customer_type?}', array('as' => 'parentcustomer.namelist', 'uses' => 'ParentCustomerController@getCustomersNameIdList'));
        Route::get('parentcustomer/single/{id}', array('as' => 'parentcustomer.single', 'uses' => 'ParentCustomerController@getSingle'));
        Route::post('parentcustomer/store', array('as' => 'parentcustomer.store', 'uses' => 'ParentCustomerController@store'));
        Route::post('parentcustomer/updateLatLong', array('as' => 'parentcustomer.updateLatLong', 'uses' => 'ParentCustomerController@updateLatLong'));
        Route::get('parentcustomer/formattedProjectDetails/{id}', array('as' => 'parentcustomer.formattedProjectDetails', 'uses' => 'ParentCustomerController@formattedProjectDetails'));
        Route::post('parentcustomer/import/process', array('as' => 'parentcustomer.import.process', 'uses' => 'ParentCustomerController@customerImport'));
        /* Parent Customer - End */

        /* Customer Shift Module - Start */
        Route::name('customer-shift-module')->get('customer-shift-module', 'ShiftModuleController@index');
        Route::get('customer-shift-module/list/{customer_id?}', array('as' => 'customer-shift-module.list', 'uses' => 'ShiftModuleController@getModuleList'));
        Route::get('customer-shift-module/add', array('as' => 'customer-shift-module.add', 'uses' => 'ShiftModuleController@addModule'));
        Route::get('customer-shift-module/update/{id}', array('as' => 'customer-shift-module.update', 'uses' => 'ShiftModuleController@addModule'));
        Route::post('customer-shift-module/store', array('as' => 'customer-shift-module.store', 'uses' => 'ShiftModuleController@storeModule'));
        Route::get('customer-shift-module/destroy', array('as' => 'customer-shift-module.destroy', 'uses' => 'ShiftModuleController@destroy'));
        Route::get('customer-shift-module/get', array('as' => 'customer-shift-module.get', 'uses' => 'ShiftModuleController@getCustomerModuleDetails'));
        Route::get('customer-shift-module/checkpostorder/{customer_id?}', array('as' => 'customer-shift-module.checkpostorder', 'uses' => 'ShiftModuleController@getPostOrderModule'));
        /* Customer Shift Module - End */

        Route::get('qrcode/getAll/{id?}', array('as' => 'qrcode.getAll', 'uses' => 'CustomerQrCodeLocationController@getList'));
        Route::post('qrcode/store', array('as' => 'qrcode.store', 'uses' => 'CustomerQrCodeLocationController@store'));
        Route::get('qrcode/single/{id}', array('as' => 'qrcode.single', 'uses' => 'CustomerQrCodeLocationController@getSingle'));

        /* Parent Customer - Start */
        Route::name('customer-rooms')->get('customer-rooms', 'CustomerRoomController@index');
        Route::get('customer-rooms/list', array('as' => 'customer-rooms.list', 'uses' => 'CustomerRoomController@getList'));
        Route::post('customer-rooms/store', array('as' => 'customer-rooms.store', 'uses' => 'CustomerRoomController@store'));
        Route::get('customer-rooms/single/{id}', array('as' => 'customer-rooms.single', 'uses' => 'CustomerRoomController@getSingle'));
        Route::get('customer-rooms/link-sensor/{id}', array('as' => 'customer-rooms.link-sensor', 'uses' => 'CustomerRoomController@getLinkSensor'));
        Route::post('customer-rooms/link-sensor/store', array('as' => 'customer-rooms.link-sensor.store', 'uses' => 'CustomerRoomController@linkSensorstore'));
        Route::get('customer-rooms/unlink-sensor/{id}', array('as' => 'customer-rooms.unlink-sensor', 'uses' => 'CustomerRoomController@getUnLinkSensor'));
        Route::post('customer-rooms/unlink-sensor/store', array('as' => 'customer-rooms.unlink-sensor.store', 'uses' => 'CustomerRoomController@unlinkSensorstore'));

        Route::get('customer-rooms/link-ipcamera/{id}', array('as' => 'customer-rooms.link-ipcamera', 'uses' => 'CustomerRoomController@getLinkIpCamera'));
        Route::post('customer-rooms/link-ipcamera/store', array('as' => 'customer-rooms.link-ipcamera.store', 'uses' => 'CustomerRoomController@linkIpCamerastore'));
        Route::get('customer-rooms/unlink-ipcamera/{id}', array('as' => 'customer-rooms.unlink-ipcamera', 'uses' => 'CustomerRoomController@getUnLinkIpCamera'));
        Route::post('customer-rooms/unlink-ipcamera/store', array('as' => 'customer-rooms.unlink-ipcamera.store', 'uses' => 'CustomerRoomController@unlinkIpCamerastore'));

        Route::get('customer-rooms/destroy/{id}', array('as' => 'customer-rooms.destroy', 'uses' => 'CustomerRoomController@destroy'));
        /* Parent Customer - End */

        /* Email Groups - Start */
        Route::name('email-groups')->get('email-groups', 'EmailGroupsController@index');
        Route::get('email-groups/list', array('as' => 'email-group.list', 'uses' => 'EmailGroupsController@getEmailGroupsList'));
        Route::post('email-groups/store', array('as' => 'email-groups.store', 'uses' => 'EmailGroupsController@store'));
        Route::get('email-groups/single/{id}', array('as' => 'email-group.single', 'uses' => 'EmailGroupsController@getSingle'));
        Route::get('email-groups/destroy/{id}', array('as' => 'email-group.destroy', 'uses' => 'EmailGroupsController@destroy'));
        /* Email Groups - End */
    });

    /* Customer - end */

    /* Customer Allocation - Start */

    Route::group(['middleware' => ['permission:customer-allocation']], function () {
        Route::name('customer-allocation')->get('customer-allocation', 'CustomerAllocationController@index');
        Route::post('customer-allocation/allocate', array('as' => 'customer-allocation.allocate', 'uses' => 'CustomerAllocationController@allocate'));
        Route::get('customer-allocation/employeeallocate', array('as' => 'customer-allocation.employeeallocate', 'uses' => 'CustomerAllocationController@employeeallocate'));
        Route::get('customer-allocation/list/{customer_id?}', array('as' => 'customer-allocation.list', 'uses' => 'CustomerAllocationController@getAllocationList'));
        Route::post('customer-allocation/unallocate', array('as' => 'customer-allocation.unallocate', 'uses' => 'CustomerAllocationController@unallocate'));
    });
    /* Customer Allocation - End */

    /* General Maters - Start */

    Route::group(['middleware' => ['permission:general_masters']], function () {

        /* Payperiod - start */
        Route::name('payperiod')->get('payperiod', 'PayPeriodController@index');
        Route::get('payperiod/list', array('as' => 'payperiod.list', 'uses' => 'PayPeriodController@getPayPeriods'));
        Route::get('payperiod/getPayPeriod', array('as' => 'payperiod.getPayPeriod', 'uses' => 'PayPeriodController@getPayPeriod'));
        Route::post('payperiod/store', array('as' => 'payperiod.store', 'uses' => 'PayPeriodController@store'));
        Route::post('payperiod/payupdate', array('as' => 'payperiod.payupdate', 'uses' => 'PayPeriodController@payperiodUpdate'));
        /* Payperiod - end */

        /* Holiday Master - start */
        Route::get('holiday', array('as' => 'holiday', 'uses' => 'HolidayController@index'));
        Route::get('holiday/list', array('as' => 'holiday.list', 'uses' => 'HolidayController@getList'));
        Route::get('holiday/statlist', array('as' => 'holiday.statlist', 'uses' => 'HolidayController@getStatList'));
        Route::post('holiday/store', array('as' => 'holiday.store', 'uses' => 'HolidayController@store'));
        Route::get('holiday/single/{id}', array('as' => 'holiday.single', 'uses' => 'HolidayController@getSingle'));
        Route::get('holiday/statsingle/{id}', array('as' => 'statholiday.single', 'uses' => 'HolidayController@getStatSingle'));
        Route::post('statholiday/store', array('as' => 'statholiday.store', 'uses' => 'HolidayController@statstore'));

        /* Holiday Master - end */

        /* Worktype - Start */
        Route::get('worktype', array('as' => 'worktype', 'uses' => 'WorkTypeController@index'));
        Route::get('worktype/list', array('as' => 'worktype.list', 'uses' => 'WorkTypeController@getList'));
        Route::post('worktype/store', array('as' => 'worktype.store', 'uses' => 'WorkTypeController@store'));
        Route::get('worktype/single/{id}', array('as' => 'worktype.single', 'uses' => 'WorkTypeController@getSingle'));
        /* Worktype - End */

        /* Position Lookupss - start */
        Route::name('position')->get('position', 'PositionLookupController@index');
        Route::get('position/list', array('as' => 'position.list', 'uses' => 'PositionLookupController@getList'));
        Route::get('position/single/{id}', array('as' => 'position.single', 'uses' => 'PositionLookupController@getSingle'));
        Route::post('position1/store', array('as' => 'position.store', 'uses' => 'PositionLookupController@store'));
        /* Position Lookupss - end */

        /* Region master - start */
        Route::name('region')->get('region', 'RegionLookupController@index');
        Route::get('region/list', array('as' => 'region.list', 'uses' => 'RegionLookupController@getList'));
        Route::get('region/single/{id}', array('as' => 'region.single', 'uses' => 'RegionLookupController@getSingle'));
        Route::post('region/store', array('as' => 'region.store', 'uses' => 'RegionLookupController@store'));
        /* Region Master - end */

        /* Industry Sector - start */
        Route::name('industry-sector')->get('industry-sector', 'IndustrySectorLookupController@index');
        Route::get('industry-sector/list', array('as' => 'industry-sector.list', 'uses' => 'IndustrySectorLookupController@getList'));
        Route::get('industry-sector/single/{id}', array('as' => 'industry-sector.single', 'uses' => 'IndustrySectorLookupController@getSingle'));
        Route::post('industry-sector/store', array('as' => 'industry-sector.store', 'uses' => 'IndustrySectorLookupController@store'));
        /* Industry Sector - end */

        /* Employee Rating - start */
        Route::name('employee-rating')->get('employee-rating', 'EmployeeRatingLookupController@index');
        Route::get('employee-rating/list', array('as' => 'employee-rating.list', 'uses' => 'EmployeeRatingLookupController@getList'));
        Route::get('employee-rating/single/{id}', array('as' => 'employee-rating.single', 'uses' => 'EmployeeRatingLookupController@getSingle'));
        Route::post('employee-rating/store', array('as' => 'employee-rating.store', 'uses' => 'EmployeeRatingLookupController@store'));
        /* Employee Rating - end */

        /* Smart Phone Types - start */
        Route::name('smart-phone-type')->get('smart-phone-type', 'SmartPhoneTypeLookupController@index');
        Route::get('smart-phone-type/list', array('as' => 'smart-phone-type.list', 'uses' => 'SmartPhoneTypeLookupController@getList'));
        Route::get('smart-phone-type/single/{id}', array('as' => 'smart-phone-type.single', 'uses' => 'SmartPhoneTypeLookupController@getSingle'));
        Route::post('smart-phone-type/store', array('as' => 'smart-phone-type.store', 'uses' => 'SmartPhoneTypeLookupController@store'));
        /* Smart Phone Types - end */

        /* Role Lookup - Start */
        Route::name('rolelookup')->get('rolelookup', 'RoleLookupController@index');
        Route::get('rolelookup/list', array('as' => 'rolelookup.list', 'uses' => 'RoleLookupController@getList'));
        Route::get('rolelookup/single/{id}', array('as' => 'rolelookup.single', 'uses' => 'RoleLookupController@getSingle'));
        Route::post('rolelookup/store', array('as' => 'rolelookup.store', 'uses' => 'RoleLookupController@store'));
        /* Role Lookup - End */

        /* User Certificate master - start */
        Route::name('user-certificate')->get('user-certificate', 'UserCertificateLookupController@index');
        Route::get('user-certificate/list', array('as' => 'user-certificate.list', 'uses' => 'UserCertificateLookupController@getList'));
        Route::get('user-certificate/single/{id}', array('as' => 'user-certificate.single', 'uses' => 'UserCertificateLookupController@getSingle'));
        Route::post('user-certificate/store', array('as' => 'user-certificate.store', 'uses' => 'UserCertificateLookupController@store'));
        /* User Certificate master  - end */

        /* Year to Date - start */
        Route::name('year-to-date')->get('year-to-date', 'YearToDateController@index');
        Route::name('year-to-date.store')->post('year-to-date/store', 'YearToDateController@store');

        /* Year to Date - end */

        /* CPID Lookup - Start */
        Route::name('cp-id')->get('cp-id', 'CpidLookupController@index');
        Route::get('cp-id/list', array('as' => 'cp-id.list', 'uses' => 'CpidLookupController@getList'));
        Route::get('cp-id/single/{id}', array('as' => 'cp-id.single', 'uses' => 'CpidLookupController@getSingle'));
        Route::post('cp-id/store', array('as' => 'cp-id.store', 'uses' => 'CpidLookupController@store'));
        Route::get('cp-id/destroy/{id}', array('as' => 'cp-id.destroy', 'uses' => 'CpidLookupController@destroy'));
        Route::get('cp-id/history/{id}', array('as' => 'cp-id.history', 'uses' => 'CpidLookupController@historyIndex'));
        Route::get('cp-id/historyList/{id}', array('as' => 'cp-id.historyList', 'uses' => 'CpidLookupController@gethistoryList'));
        Route::get('cp-id/check-cpid-allocation/{id}', array('as' => 'cp-id.check-cpid-allocation', 'uses' => 'CpidLookupController@checkCpidAllocation'));

        /* CPID Lookup - End */

        /// Cpid Function
        Route::get('cpid-function', array('as' => 'admin.cpid-fn.view', 'uses' => 'CpidFunctionConroller@index'));
        Route::get('cpid-function/list', array('as' => 'admin.cpid-fn.list', 'uses' => 'CpidFunctionConroller@getList'));
        Route::post('cpid-function/store', array('as' => 'admin.cpid-fn.store', 'uses' => 'CpidFunctionConroller@store'));
        Route::get('cpid-function/single/{id}', array('as' => 'admin.cpid-fn.single', 'uses' => 'CpidFunctionConroller@getById'));
        Route::get('cpid-function/destroy/{id}', array('as' => 'admin.cpid-fn.destroy', 'uses' => 'CpidFunctionConroller@destroy'));


        ///Customer Types
        Route::get('customer-types', array('as' => 'admin.customer-types.view', 'uses' => 'CustomerTypeController@index'));
        Route::get('customer-types/list', array('as' => 'admin.customer-types.list', 'uses' => 'CustomerTypeController@getList'));
        Route::post('customer-types/store', array('as' => 'admin.customer-types.store', 'uses' => 'CustomerTypeController@store'));
        Route::get('customer-types/single/{id}', array('as' => 'admin.customer-types.single', 'uses' => 'CustomerTypeController@getById'));
        Route::get('customer-types/destroy/{id}', array('as' => 'admin.customer-types.destroy', 'uses' => 'CustomerTypeController@destroy'));

        /* Banks - Start */
        Route::name('banks')->get('banks', 'BanksController@index');
        Route::get('banks/list', array('as' => 'banks.list', 'uses' => 'BanksController@getList'));
        Route::get('banks/single/{id}', array('as' => 'banks.single', 'uses' => 'BanksController@getSingle'));
        Route::post('banks/store', array('as' => 'banks.store', 'uses' => 'BanksController@store'));
        /* Banks - End */

        /* Salutation - Start */
        Route::name('salutation')->get('salutation', 'UserSalutationController@index');
        Route::get('salutation/list', array('as' => 'salutation.list', 'uses' => 'UserSalutationController@getList'));
        Route::get('salutation/single/{id}', array('as' => 'salutation.single', 'uses' => 'UserSalutationController@getSingle'));
        Route::post('salutation/store', array('as' => 'salutation.store', 'uses' => 'UserSalutationController@store'));
        /* Salutation - End */

        /* Relation - Start */
        Route::name('user-emergency-contact-relation')->get('user-emergency-contact-relation', 'UserEmergencyContactRelationController@index');
        Route::get('user-emergency-contact-relation/list', array('as' => 'user-emergency-contact-relation.list', 'uses' => 'UserEmergencyContactRelationController@getList'));
        Route::get('user-emergency-contact-relation/single/{id}', array('as' => 'user-emergency-contact-relation.single', 'uses' => 'UserEmergencyContactRelationController@getSingle'));
        Route::post('user-emergency-contact-relation/store', array('as' => 'user-emergency-contact-relation.store', 'uses' => 'UserEmergencyContactRelationController@store'));
        /* Relation - End */

        /*  Experience Wise Leave Master - Start */
        Route::name('experience-wise-leave-master')->get('experience-wise-leave-master', 'ExperienceWiseLeaveMasterController@index');
        Route::get('experience-wise-leave-master/list', array('as' => 'experience-wise-leave-master.list', 'uses' => 'ExperienceWiseLeaveMasterController@getList'));
        Route::get('experience-wise-leave-master/single/{id}', array('as' => 'experience-wise-leave-master.single', 'uses' => 'ExperienceWiseLeaveMasterController@getSingle'));
        Route::post('experience-wise-leave-master/store', array('as' => 'experience-wise-leave-master.store', 'uses' => 'ExperienceWiseLeaveMasterController@store'));
        Route::get('experience-wise-leave-master/destroy/{id}', array('as' => 'experience-wise-leave-master.destroy', 'uses' => 'ExperienceWiseLeaveMasterController@destroy'));
        /*  Experience Wise Leave Master - End */
        /* User Skills - Start */
        Route::name('user-skills')->get('user-skills', 'UserSkillLookupController@index');
        Route::get('user-skills/list', array('as' => 'user-skills.list', 'uses' => 'UserSkillLookupController@getList'));
        Route::get('user-skills/single/{id}', array('as' => 'user-skills.single', 'uses' => 'UserSkillLookupController@getSingle'));
        Route::post('user-skills/store', array('as' => 'user-skills.store', 'uses' => 'UserSkillLookupController@store'));
        Route::get('user-skills/destroy/{id}', array('as' => 'user-skills.destroy', 'uses' => 'UserSkillLookupController@destroy'));
        /* User Skills - End */
        /* User Skill Option - Start */
        Route::name('user-skill-option')->get('user-skill-option', 'UserSkillOptionController@index');
        Route::get('user-skill-option/add', array('as' => 'user-skill-option.add', 'uses' => 'UserSkillOptionController@addOption'));
        Route::post('user-skill-option/store', array('as' => 'user-skill-option.store', 'uses' => 'UserSkillOptionController@store'));
        Route::get('user-skill-option/list', array('as' => 'user-skill-option.list', 'uses' => 'UserSkillOptionController@getList'));
        Route::get('user-skill-option/update/{id}', array('as' => 'user-skill-option.update', 'uses' => 'UserSkillOptionController@addOption'));
        Route::name('user-skill-option-value.destroy')->delete('user-skill-option-value/{id}/destroy', 'UserSkillOptionController@destroyOptionValue');
        Route::get('user-skill-option-value/single/{id}', array('as' => 'user-skill-option-value.single', 'uses' => 'UserSkillOptionController@getSkillValue'));


        // Route::post('user-skills/store', array('as' => 'user-skills.store', 'uses' => 'UserSkillLookupController@store'));
        Route::name('vacation-entitlement')->get('vacation-entitlement', 'VacationController@index');
        Route::get('vacation-entitlement/list', array('as' => 'vacation-entitlement.list', 'uses' => 'VacationController@getList'));
        Route::get('vacation-entitlement/single/{id}', array('as' => 'vacation-entitlement.single', 'uses' => 'VacationController@getSingle'));
        Route::post('vacation-entitlement/store', array('as' => 'vacation-entitlement.store', 'uses' => 'VacationController@store'));
        Route::get('vacation-entitlement/destroy/{id}', array('as' => 'vacation-entitlement.destroy', 'uses' => 'VacationController@destroy'));

        Route::name('mangement-role')->get('mangement-role', 'ManagementRoleController@index');
        Route::get('mangement-role/list', array('as' => 'mangement-role.list', 'uses' => 'ManagementRoleController@getList'));
        Route::get('mangement-role/single/{id}', array('as' => 'mangement-role.single', 'uses' => 'ManagementRoleController@getSingle'));
        Route::post('mangement-role/store', array('as' => 'mangement-role.store', 'uses' => 'ManagementRoleController@store'));
        Route::get('mangement-role/destroy/{id}', array('as' => 'mangement-role.destroy', 'uses' => 'ManagementRoleController@destroy'));
        /* User Skill Option - End */
    });
    /* General Maters - End */

    /* Recruting Maters - Start */

    Route::group(['middleware' => ['permission:recruiting_masters']], function () {

        /* Job requisition reasons - start */
        Route::name('job-requisition-reason')->get('job-requisition-reason', 'JobRequisitionReasonLookupController@index');
        Route::get('job-requisition-reason/list', array('as' => 'job-requisition-reason.list', 'uses' => 'JobRequisitionReasonLookupController@getList'));
        Route::get('job-requisition-reason/single/{id}', array('as' => 'job-requisition-reason.single', 'uses' => 'JobRequisitionReasonLookupController@getSingle'));
        Route::post('job-requisition-reason/store', array('as' => 'job-requisition-reason.store', 'uses' => 'JobRequisitionReasonLookupController@store'));
        /* Job requisition reasons - end */

        /* Employee ratings -start */
        Route::name('rating-policy')->get('rating-policy', 'EmployeeRatingController@index');
        Route::get('rating-policy/list', array('as' => 'rating-policy.list', 'uses' => 'EmployeeRatingController@getList'));
        Route::get('rating-policy/single/{id}', array('as' => 'rating-policy.single', 'uses' => 'EmployeeRatingController@getSingle'));
        Route::post('rating-policy/store', array('as' => 'rating-policy.store', 'uses' => 'EmployeeRatingController@store'));
        /* Employee ratings -end */

        /* Candidate Assignment Types Lookup - start */
        Route::name('candidate-assignment-type')->get('candidate-assignment-type', 'CandidateAssignmentTypeLookupController@index');
        Route::get('candidate-assignment-type/list', array('as' => 'candidate-assignment-type.list', 'uses' => 'CandidateAssignmentTypeLookupController@getList'));
        Route::get('candidate-assignment-type/single/{id}', array('as' => 'candidate-assignment-type.single', 'uses' => 'CandidateAssignmentTypeLookupController@getSingle'));
        Route::post('candidate-assignment-type/store', array('as' => 'candidate-assignment-type.store', 'uses' => 'CandidateAssignmentTypeLookupController@store'));

        /* Candidate Assignment Types Lookup - end */

        /* Training Lookup - start */
        Route::name('training')->get('training', 'TrainingLookupController@index');
        Route::get('training/list', array('as' => 'training.list', 'uses' => 'TrainingLookupController@getList'));
        Route::get('training/single/{id}', array('as' => 'training.single', 'uses' => 'TrainingLookupController@getSingle'));
        Route::post('training/store', array('as' => 'training.store', 'uses' => 'TrainingLookupController@store'));

        /* Training Lookup - end */

        /* Timing Lookup - start */
        Route::name('training-timing')->get('training-timing', 'TrainingTimingLookupController@index');
        Route::get('training-timing/list', array('as' => 'training-timing.list', 'uses' => 'TrainingTimingLookupController@getList'));
        Route::get('training-timing/single/{id}', array('as' => 'training-timing.single', 'uses' => 'TrainingTimingLookupController@getSingle'));
        Route::post('training-timing/store', array('as' => 'training-timing.store', 'uses' => 'TrainingTimingLookupController@store'));

        /* Timing Lookup - end */

        /* Criteria Lookup - start */
        Route::name('criteria')->get('criteria', 'CriteriaLookupController@index');
        Route::get('criteria/list', array('as' => 'criteria.list', 'uses' => 'CriteriaLookupController@getList'));
        Route::get('criteria/single/{id}', array('as' => 'criteria.single', 'uses' => 'CriteriaLookupController@getSingle'));
        Route::post('criteria/store', array('as' => 'criteria.store', 'uses' => 'CriteriaLookupController@store'));

        /* Criteria Lookup - end */

        /* Experince Lookup - start */
        Route::name('candidate-experience')->get('candidate-experience', 'CandidateExperienceLookupController@index');
        Route::get('candidate-experience/list', array('as' => 'candidate-experience.list', 'uses' => 'CandidateExperienceLookupController@getList'));
        Route::get('candidate-experience/single/{id}', array('as' => 'candidate-experience.single', 'uses' => 'CandidateExperienceLookupController@getSingle'));
        Route::post('candidate-experience/store', array('as' => 'candidate-experience.store', 'uses' => 'CandidateExperienceLookupController@store'));

        /* Experince Lookup - end */

        /* Feedback Lookup - start */
        Route::name('candidate-feedback-lookup')->get('candidate-feedback-lookup', 'FeedbackLookupController@index');
        Route::get('candidate-feedback-lookup/list', array('as' => 'candidate-feedback-lookup.list', 'uses' => 'FeedbackLookupController@getList'));
        Route::get('candidate-feedback-lookup/single/{id}', array('as' => 'candidate-feedback-lookup.single', 'uses' => 'FeedbackLookupController@getSingle'));
        Route::post('candidate-feedback-lookup/store', array('as' => 'candidate-feedback-lookup.store', 'uses' => 'FeedbackLookupController@store'));

        /* Feedback Lookup - end */

        /* Tracking Process lookup- start */
        Route::name('tracking-lookup')->get('tracking-lookup', 'TrackingProcessLookupController@index');
        Route::get('tracking-lookup/list', array('as' => 'tracking-lookup.list', 'uses' => 'TrackingProcessLookupController@getList'));
        Route::get('tracking-lookup/single/{id}', array('as' => 'tracking-lookup.single', 'uses' => 'TrackingProcessLookupController@getSingle'));
        Route::post('tracking-lookup/store', array('as' => 'tracking-lookup.store', 'uses' => 'TrackingProcessLookupController@store'));

        /* Training Process lookup- end */

        /* Security Clearance Lookup - start */
        Route::name('security-clearance')->get('security-clearance', 'SecurityClearanceLookupController@index');
        Route::get('security-clearance/list', array('as' => 'security-clearance.list', 'uses' => 'SecurityClearanceLookupController@getList'));
        Route::get('security-clearance/single/{id}', array('as' => 'security-clearance.single', 'uses' => 'SecurityClearanceLookupController@getSingle'));
        Route::post('security-clearance/store', array('as' => 'security-clearance.store', 'uses' => 'SecurityClearanceLookupController@store'));
        /* Security Clearance Lookup - end */

        /* Assignment Lookup - start */
        Route::name('schedule-assignment-type')->get('schedule-assignment-type', 'ScheduleAssignmentTypeLookupController@index');
        Route::get('schedule-assignment-type/list', array('as' => 'schedule-assignment-type.list', 'uses' => 'ScheduleAssignmentTypeLookupController@getList'));
        Route::get('schedule-assignment-type/single/{id}', array('as' => 'schedule-assignment-type.single', 'uses' => 'ScheduleAssignmentTypeLookupController@getSingle'));
        Route::post('schedule-assignment-type/store', array('as' => 'schedule-assignment-type.store', 'uses' => 'ScheduleAssignmentTypeLookupController@store'));

        /* Assignment Lookup - end */

        /* Candidate Termination - start */
        Route::name('candidate-termination-reason')->get('candidate-termination-reason', 'CandidateTerminationReasonLookupController@index');
        Route::get('candidate-termination-reason/list', array('as' => 'candidate-termination-reason.list', 'uses' => 'CandidateTerminationReasonLookupController@getList'));
        Route::get('candidate-termination-reason/single/{id}', array('as' => 'candidate-termination-reason.single', 'uses' => 'CandidateTerminationReasonLookupController@getSingle'));
        Route::post('candidate-termination-reason/store', array('as' => 'candidate-termination-reason.store', 'uses' => 'CandidateTerminationReasonLookupController@store'));

        /* Candidate Termination - end */

        /* Exit Termination Reasons -start */
        Route::name('exit-termination-reason')->get('exit-termination-reason', 'ExitTerminationReasonLookupController@reason');
        Route::get('exit-termination-reason/list', array('as' => 'exit-termination-reason.list', 'uses' => 'ExitTerminationReasonLookupController@list'));
        Route::get('exit-termination-reason/single/{id}', array('as' => 'exit-termination-reason.single', 'uses' => 'ExitTerminationReasonLookupController@single'));
        Route::post('exit-termination-reason/store', array('as' => 'exit-termination-reason.store', 'uses' => 'ExitTerminationReasonLookupController@save'));
        Route::get('exit-termination-reason/destroy/{id}', array('as' => 'exit-termination-reason.destroy', 'uses' => 'ExitTerminationReasonLookupController@destroy'));

        /* Exit Termination Reasons -End */

        /* Employee exit interview regination details */
        Route::name('exit-resignation-reason')->get('exit-resignation-reason', 'ExitResignationReasonLookupController@index');
        Route::get('exit-resignation-reason/list', array('as' => 'exit-resignation-reason.list', 'uses' => 'ExitResignationReasonLookupController@getList'));
        Route::get('exit-resignation-reason/single/{id}', array('as' => 'exit-resignation-reason.single', 'uses' => 'ExitResignationReasonLookupController@getSingle'));
        Route::post('exit-resignation-reason/store', array('as' => 'exit-resignation-reason.store', 'uses' => 'ExitResignationReasonLookupController@store'));

        /* Employee exit interview resgination details end */

        /* Employee whistle blower - Start */
        Route::name('employee-whistleblower-category')->get('employee-whistleblower-category', 'EmployeeWhistleblowerCategoryController@index');
        Route::get('employee-whistleblower-category/list', array('as' => 'employee-whistleblower-category.list', 'uses' => 'EmployeeWhistleblowerCategoryController@getList'));
        Route::get('employee-whistleblower-category/single/{id}', array('as' => 'employee-whistleblower-category.single', 'uses' => 'EmployeeWhistleblowerCategoryController@getSingle'));
        Route::post('employee-whistleblower-category/store', array('as' => 'employee-whistleblower-category.store', 'uses' => 'EmployeeWhistleblowerCategoryController@store'));

        /* Employee whitsle ends here */

        /* Employee whistle blower Priority - Start */
        Route::name('employee-whistleblower-priority')->get('employee-whistleblower-priority', 'EmployeeWhistleblowerPriorityController@index');
        Route::get('employee-whistleblower-priority/list', array('as' => 'employee-whistleblower-priority.list', 'uses' => 'EmployeeWhistleblowerPriorityController@getList'));
        Route::post('employee-whistleblower-priority/store', array('as' => 'employee-whistleblower-priority.store', 'uses' => 'EmployeeWhistleblowerPriorityController@store'));
        Route::get('employee-whistleblower-priority/single/{id}', array('as' => 'employee-whistleblower-priority.single', 'uses' => 'EmployeeWhistleblowerPriorityController@getSingle'));

        /* Employee whistle blower Priority - End */

        /* Candidate Brand Awareness - start */
        Route::name('candidate-brand-awareness')->get('candidate-brand-awareness', 'CandidateBrandAwarenessController@index');
        Route::get('candidate-brand-awareness/list', array('as' => 'candidate-brand-awareness.list', 'uses' => 'CandidateBrandAwarenessController@getList'));
        Route::get('candidate-brand-awareness/lookupList', array('as' => 'candidate-brand-awareness.lookupList', 'uses' => 'CandidateBrandAwarenessController@lookupList'));
        Route::get('candidate-brand-awareness/single/{id}', array('as' => 'candidate-brand-awareness.single', 'uses' => 'CandidateBrandAwarenessController@getSingle'));
        Route::post('candidate-brand-awareness/store', array('as' => 'candidate-brand-awareness.store', 'uses' => 'CandidateBrandAwarenessController@store'));

        /* Candidate Brand Awareness - end */

        /* Candidate security - start */
        Route::name('candidate-security-awareness')->get('candidate-security-awareness', 'CandidateSecurityAwarenessController@index');
        Route::get('candidate-security-awareness/list', array('as' => 'candidate-security-awareness.list', 'uses' => 'CandidateSecurityAwarenessController@getList'));
        Route::get('candidate-security-awareness/lookupList', array('as' => 'candidate-security-awareness.lookupList', 'uses' => 'CandidateSecurityAwarenessController@lookupList'));
        Route::get('candidate-security-awareness/single/{id}', array('as' => 'candidate-security-awareness.single', 'uses' => 'CandidateSecurityAwarenessController@getSingle'));
        Route::post('candidate-security-awareness/store', array('as' => 'candidate-security-awareness.store', 'uses' => 'CandidateSecurityAwarenessController@store'));

        /* Candidate Brand Awareness - end */

        /* Mapping shift and timings- start */
        Route::name('schedule-shift-timings')->get('schedule-shift-timings', 'ScheduleShiftTimingsController@index');
        Route::get('schedule-shift-timings/list', array('as' => 'schedule-shift-timings.list', 'uses' => 'ScheduleShiftTimingsController@getList'));
        Route::get('schedule-shift-timings/single/{shift_name}', array('as' => 'schedule-shift-timings.single', 'uses' => 'ScheduleShiftTimingsController@getSingle'));
        Route::post('schedule-shift-timings/store', array('as' => 'schedule-shift-timings.store', 'uses' => 'ScheduleShiftTimingsController@store'));

        /* Mapping shift and timings - end */

        /* Maximum Hour Timing - start */
        Route::name('schedule-maximum-hours')->get('schedule-maximum-hours', 'ScheduleMaximumHoursController@index');
        Route::name('schedule-maximum-hours.store')->post('schedule-maximum-hours/store', 'ScheduleMaximumHoursController@store');

        /* Maximum Hour Timing - end */

        /* Competency-matrix - start */
        Route::name('competency-matrix-category')->get('competency-matrix-category', 'CompetencyMatrixCategoryLookupController@index');
        Route::name('competency-matrix-category.list')->get('competency-matrix-category/list', 'CompetencyMatrixCategoryLookupController@getList');
        Route::name('competency-matrix-category.single')->get('competency-matrix-category/{id}/single', 'CompetencyMatrixCategoryLookupController@get');
        Route::name('competency-matrix-category.store')->post('competency-matrix-category/store', 'CompetencyMatrixCategoryLookupController@store');

        /* Competency-matrix - End */

        /* Competency Matrix - start */
        Route::name('competency-matrix')->get('competency-matrix', 'CompetencyMatrixLookupController@index');
        Route::name('competency-matrix.list')->get('competency-matrix/list', 'CompetencyMatrixLookupController@getList');
        Route::name('competency-matrix.single')->get('competency-matrix/{id}/single', 'CompetencyMatrixLookupController@get');
        Route::name('competency-matrix.store')->post('competency-matrix/store', 'CompetencyMatrixLookupController@store');

        /* Competency Matrix - end */

        /* English Rating - start */
        Route::name('english-rating')->get('english-rating', 'EnglishRatingLookupController@index');
        Route::get('english-rating/list', array('as' => 'english-rating.list', 'uses' => 'EnglishRatingLookupController@getList'));
        Route::get('english-rating/single/{id}', array('as' => 'english-rating.single', 'uses' => 'EnglishRatingLookupController@getSingle'));
        Route::post('english-rating/store', array('as' => 'english-rating.store', 'uses' => 'EnglishRatingLookupController@store'));

        /* English Rating - end */

        /* Competency Matrix Rating- Start */
        Route::name('competency-matrix-rating')->get('competency-matrix-rating', 'CompetencyMatrixRatingLookupController@index');
        Route::name('competency-matrix-rating.list')->get('competency-matrix-rating/list', 'CompetencyMatrixRatingLookupController@getList');
        Route::name('competency-matrix-rating.single')->get('competency-matrix-rating/{id}/single', 'CompetencyMatrixRatingLookupController@get');
        Route::name('competency-matrix-rating.store')->post('competency-matrix-rating/store', 'CompetencyMatrixRatingLookupController@store');

        /* Competency Matrix Rating- end */

        /* Rate Experiences - start */
        Route::name('rate-experiences')->get('rate-experiences', 'RateExperienceLookupController@index');
        Route::get('rate-experiences/list', array('as' => 'rate-experiences.list', 'uses' => 'RateExperienceLookupController@getList'));
        Route::get('rate-experiences/single/{id}', array('as' => 'rate-experiences.single', 'uses' => 'RateExperienceLookupController@getSingle'));
        Route::post('rate-experiences/store', array('as' => 'rate-experiences.store', 'uses' => 'RateExperienceLookupController@store'));

        /* Rate Experiences - end */

        /* Commissionaires Understanding - start */
        Route::name('commissionaires-understanding')->get('commissionaires-understanding', 'CommissionairesUnderstandingLookupController@index');
        Route::get('commissionaires-understanding/list', array('as' => 'commissionaires-understanding.list', 'uses' => 'CommissionairesUnderstandingLookupController@getList'));
        Route::get('commissionaires-understanding/single/{id}', array('as' => 'commissionaires-understanding.single', 'uses' => 'CommissionairesUnderstandingLookupController@getSingle'));
        Route::post('commissionaires-understanding/store', array('as' => 'commissionaires-understanding.store', 'uses' => 'CommissionairesUnderstandingLookupController@store'));

        /* Commissionaires Understanding - end */

        /* Licence threshold - start */
        Route::name('licence-threshold')->get('licence-threshold', 'LicenceThresholdController@index');
        // Route::get('commissionaires-understanding/list', array('as' => 'commissionaires-understanding.list', 'uses' => 'CommissionairesUnderstandingLookupController@getList'));
        // Route::get('commissionaires-understanding/single/{id}', array('as' => 'commissionaires-understanding.single', 'uses' => 'CommissionairesUnderstandingLookupController@getSingle'));
        Route::post('licence-threshold/store', array('as' => 'licence-threshold.store', 'uses' => 'LicenceThresholdController@store'));

        /* Licence threshold - end */

        /* Job Ticket Setting - start */
        Route::get('job-ticket-settings', array('as' => 'job-ticket-settings', 'uses' => 'JobTicketSettingsController@index'));
        Route::post('job-ticket-settings/store', array('as' => 'job-ticket-settings.store', 'uses' => 'JobTicketSettingsController@store'));
        /* Job Ticket Setting - end */

        /* Job Post Finding Lookup - start */
        Route::name('job-post-finding')->get('job-post-finding', 'JobPostFindingLookupController@index');
        Route::get('job-post-finding/list', array('as' => 'job-post-finding.list', 'uses' => 'JobPostFindingLookupController@getList'));
        Route::get('job-post-finding/single/{id}', array('as' => 'job-post-finding.single', 'uses' => 'JobPostFindingLookupController@getSingle'));
        Route::post('job-post-finding/store', array('as' => 'job-post-finding.store', 'uses' => 'JobPostFindingLookupController@store'));
        Route::get('job-post-finding/destroy/{id}', array('as' => 'job-post-finding.destroy', 'uses' => 'JobPostFindingLookupController@destroy'));
        /* Job Post Finding Lookup - end */
        Route::name('whistleblower-master')->get('whistleblower-master', 'WhistleblowerStatusLookupController@index');
        Route::get('whistleblower-master/list', array('as' => 'whistleblower-master.list', 'uses' => 'WhistleblowerStatusLookupController@getList'));
        Route::get('whistleblower-master/single/{id}', array('as' => 'whistleblower-master.single', 'uses' => 'WhistleblowerStatusLookupController@getSingle'));
        Route::post('whistleblower-master/store', array('as' => 'whistleblower-master.store', 'uses' => 'WhistleblowerStatusLookupController@store'));
        Route::get('whistleblower-master/intial-status/{id}', array('as' => 'whistleblower-master.intial-status', 'uses' => 'WhistleblowerStatusLookupController@storeIntialStatus'));
        Route::delete('whistleblower-master/destroy/{id}', array('as' => 'whistleblower-master.destroy', 'uses' => 'WhistleblowerStatusLookupController@destroy'));
        /* Relation - End */
    });

    /* Recruting Maters - End */

    /* Department Master - end */
    Route::name('department-master')->get('department-master', 'DepartmentMasterController@index');
    Route::get('department-master/list', array('as' => 'department-master.list', 'uses' => 'DepartmentMasterController@getList'));
    Route::get('department-master/single/{id}', array('as' => 'department-master.single', 'uses' => 'DepartmentMasterController@getSingle'));
    Route::post('department-master/store', array('as' => 'department-master.store', 'uses' => 'DepartmentMasterController@store'));
    Route::get('department-master/destroy/{id}', array('as' => 'department-master.destroy', 'uses' => 'DepartmentMasterController@destroy'));
    Route::get('department-master/allocate-employee/{id}', array('as' => 'department-master.allocate-employee', 'uses' => 'DepartmentMasterController@allocateEmployee'));
    Route::post('department-master/employee-mapping/store', array('as' => 'department-master.employee-mapping.store', 'uses' => 'DepartmentMasterController@storeEmployeeMapping'));
    /* Department Master - End */

    /* Key management Maters - Start */

    Route::group(['middleware' => ['permission:key_management_lookups']], function () {
        /* Id Type Lookup - Start */
        Route::name('identification-document')->get('identification-document', 'IdentificationDocumentLookupController@index');
        Route::get('identification-document/list', array('as' => 'identification-document.list', 'uses' => 'IdentificationDocumentLookupController@getList'));
        Route::get('identification-document/single/{id}', array('as' => 'identification-document.single', 'uses' => 'IdentificationDocumentLookupController@getSingle'));
        Route::post('identification-document/store', array('as' => 'identification-document.store', 'uses' => 'IdentificationDocumentLookupController@store'));
        Route::get('identification-document/destroy/{id}', array('as' => 'identification-document.destroy', 'uses' => 'IdentificationDocumentLookupController@destroy'));
        /* Id Type Lookup - End */
    });
    // Route::get('templates/add', array('as' => 'templates.add', 'uses' => 'TemplateController@addTemplate'));
    //         Route::post('templates/add', array('as' => 'templates.add', 'uses' => 'TemplateController@storeTemplate'));
    //         Route::get('templates/update/{id}', array('as' => 'templates.update', 'uses' => 'TemplateController@addTemplate'));
    //         Route::post('templates/update/{id}', array('as' => 'templates.update', 'uses' => 'TemplateController@storeTemplate'));
    /* Key management Maters - End */

    /* Employee Survey - Start */
    Route::group(['middleware' => ['permission:employee_survey_admin']], function () {

        Route::name('employee-survey-template')->get('employee-survey-template', 'EmployeeSurveyTemplateController@index');
        Route::get('employee-survey-template/list', array('as' => 'employee-survey-template.list', 'uses' => 'EmployeeSurveyTemplateController@getList'));
        Route::get('employee-survey-template/add', array('as' => 'employee-survey-template.add', 'uses' => 'EmployeeSurveyTemplateController@addTemplate'));
        Route::post('employee-survey-template/store', array('as' => 'employee-survey-template.store', 'uses' => 'EmployeeSurveyTemplateController@store'));
        // Route::get('customershift/destroy/{id}', array('as' => 'customershift.destroy', 'uses' => 'CustomerShiftController@destroy'));
        Route::get('employee-survey-template/update/{id}/{is_view?}', array('as' => 'employee-survey-template.update', 'uses' => 'EmployeeSurveyTemplateController@addTemplate'));
        Route::post('employee-survey-template/update/{id}', array('as' => 'employee-survey-template.update', 'uses' => 'EmployeeSurveyTemplateController@store'));
        // Route::get('employee-survey-template/single/{id}', array('as' => 'employee-survey-template.single', 'uses' => 'EmployeeSurveyTemplateController@getSingle'));
    });
    /* Employee Survey - End */

    /* Employee Time off Masters - Start */

    Route::group(['middleware' => ['permission:employee_timeoff_masters']], function () {

        /* Time Off Category - start */
        Route::name('time-off-category')->get('time-off-category', 'TimeOffCategoryLookupController@index');
        Route::get('time-off-category/list', array('as' => 'time-off-category.list', 'uses' => 'TimeOffCategoryLookupController@getList'));
        Route::get('time-off-category-type/lookupList', array('as' => 'time-off-category-type.lookupList', 'uses' => 'TimeOffCategoryLookupController@lookupList'));
        Route::get('time-off-category/single/{id}', array('as' => 'time-off-category.single', 'uses' => 'TimeOffCategoryLookupController@getSingle'));
        Route::post('time-off-category/store', array('as' => 'time-off-category.store', 'uses' => 'TimeOffCategoryLookupController@store'));

        /* Time Off Category - end */

        /* Time Off Request Type - start */
        Route::name('time-off-request-type')->get('time-off-request-type', 'TimeOffRequestTypeLookupController@index');
        Route::get('time-off-request-type/list', array('as' => 'time-off-request-type.list', 'uses' => 'TimeOffRequestTypeLookupController@getList'));
        Route::get('time-off-request-type/lookupList', array('as' => 'time-off-request-type.lookupList', 'uses' => 'TimeOffRequestTypeLookupController@lookupList'));
        Route::get('time-off-request-type/single/{id}', array('as' => 'time-off-request-type.single', 'uses' => 'TimeOffRequestTypeLookupController@getSingle'));
        Route::post('time-off-request-type/store', array('as' => 'time-off-request-type.store', 'uses' => 'TimeOffRequestTypeLookupController@store'));

        /* Time Off Request Type - end */

        /* Operation Centre Email - start */
        Route::name('operation-centre-email')->get('operation-centre-email', 'OperationCentreEmailController@index');
        Route::get('operation-centre-email/list', array('as' => 'operation-centre-email.list', 'uses' => 'OperationCentreEmailController@getList'));
        Route::get('operation-centre-email/lookupList', array('as' => 'operation-centre-email.lookupList', 'uses' => 'OperationCentreEmailController@lookupList'));
        Route::get('operation-centre-email/single/{id}', array('as' => 'operation-centre-email.single', 'uses' => 'OperationCentreEmailController@getSingle'));
        Route::post('operation-centre-email/store', array('as' => 'operation-centre-email.store', 'uses' => 'OperationCentreEmailController@store'));

        /* Operation Centre Email - end */
    });

    /* Employee Time off Masters - End */

    /* Supervisor Panel Masters - Start */

    Route::group(['middleware' => ['permission:supervisor_panel_masters']], function () {

        /* Templates - start */
        Route::get('templates', array('as' => 'templates', 'uses' => 'TemplateController@index'));
        Route::get('templates/list', array('as' => 'templates.list', 'uses' => 'TemplateController@getList'));
        Route::get('templates/add', array('as' => 'templates.add', 'uses' => 'TemplateController@addTemplate'));
        Route::post('templates/add', array('as' => 'templates.add', 'uses' => 'TemplateController@storeTemplate'));
        Route::get('templates/update/{id}', array('as' => 'templates.update', 'uses' => 'TemplateController@addTemplate'));
        Route::post('templates/update/{id}', array('as' => 'templates.update', 'uses' => 'TemplateController@storeTemplate'));

        /* Templates - End */

        /* Templates Settings - start */
        Route::get('templatesettings', array('as' => 'templatesettings', 'uses' => 'TemplateSettingController@index'));
        Route::post('templatesettings', array('as' => 'templatesettings', 'uses' => 'TemplateSettingController@storeTemplateSettings'));

        /* Templates - start */

        /* Questions category - start */
        Route::name('templatequestioncategory')->get('templatequestioncategory', 'TemplateQuestionsCategoryController@index');
        Route::get('templatequestioncategory/list', array('as' => 'templatequestioncategory.list', 'uses' => 'TemplateQuestionsCategoryController@getList'));
        Route::get('templatequestioncategory/single', array('as' => 'templatequestioncategory.single', 'uses' => 'TemplateQuestionsCategoryController@getSingle'));
        Route::post('templatequestioncategory/store', array('as' => 'templatequestioncategory.store', 'uses' => 'TemplateQuestionsCategoryController@store'));

        /* Questions category - end */

        /* Subject category - start */
        Route::name('incidentreportsubjects')->get('incidentreportsubjects', 'IncidentReportSubjectsController@index');
        Route::get('incidentreportsubjects/list', array('as' => 'incidentreportsubjects.list', 'uses' => 'IncidentReportSubjectsController@getList'));
        Route::get('incidentreportsubjects/getSubjectLookup/{customer_id?}', array('as' => 'incidentreportsubjects.getSubjectLookup', 'uses' => 'IncidentReportSubjectsController@getSubjectLookup'));
        Route::get('incidentreportsubjects/single/{id}', array('as' => 'incidentreportsubjects.single', 'uses' => 'IncidentReportSubjectsController@getSingle'));
        Route::post('incidentreportsubjects/store', array('as' => 'incidentreportsubjects.store', 'uses' => 'IncidentReportSubjectsController@store'));
        //Incident Category
        Route::get('incident_categories/list', array('as' => 'incident_categories.list', 'uses' => 'IncidentCategoryController@list'));
        Route::resource('incident_categories', 'IncidentCategoryController');
        /* Subject category - end */

        /* Leave Reason - start */
        Route::name('leavereasons')->get('leavereasons', 'LeaveReasonController@index');
        Route::get('leavereasons/list', array('as' => 'leavereasons.list', 'uses' => 'LeaveReasonController@getList'));
        Route::get('leavereasons/single/{id}', array('as' => 'leavereasons.single', 'uses' => 'LeaveReasonController@getSingle'));
        Route::post('leavereasons/store', array('as' => 'leavereasons.store', 'uses' => 'LeaveReasonController@store'));

        /* Leave Reason - end */

        /* Site Note Status - start */
        Route::name('sitestatus')->get('sitestatus', 'SiteNoteStatusLookupController@index');
        Route::get('sitestatus/list', array('as' => 'sitestatus.list', 'uses' => 'SiteNoteStatusLookupController@getList'));
        Route::get('sitestatus/single/{id}', array('as' => 'sitestatus.single', 'uses' => 'SiteNoteStatusLookupController@getSingle'));
        Route::post('sitestatus/store', array('as' => 'sitestatus.store', 'uses' => 'SiteNoteStatusLookupController@store'));

        /*  Site Note Status - end */

        /* STC Report Template Rule - start */
        Route::get('stc-template-rule', array('as' => 'stc-template-rule', 'uses' => 'StcReportTemplateRuleController@index'));
        Route::post('stc-template-rule/store', array('as' => 'stc-template-rule.store', 'uses' => 'StcReportTemplateRuleController@storeTemplateSettings'));

        /* STC Report Template Rule - end */

        /* Incident Priority Lookup - Start */
        Route::name('incident-priority')->get('incident-priority', 'IncidentPriorityLookupController@index');
        Route::get('incident-priority/list', array('as' => 'incident-priority.list', 'uses' => 'IncidentPriorityLookupController@getList'));
        Route::get('incident-priority/single/{id}', array('as' => 'incident-priority.single', 'uses' => 'IncidentPriorityLookupController@getSingle'));
        Route::post('incident-priority/store', array('as' => 'incident-priority.store', 'uses' => 'IncidentPriorityLookupController@store'));
        Route::get('incident-priority/destroy/{id}', array('as' => 'incident-priority.destroy', 'uses' => 'IncidentPriorityLookupController@destroy'));

        /* Incident Priority Lookup - End */

        /* Customer Incident Subject Mapping - Start */

        Route::get('customer-incident-priority/check/{id}', array('as' => 'customer-incident-priority.check', 'uses' => 'CustomerIncidentPriorityController@checkPriority'));
        Route::post('customer-incident-priority/store', array('as' => 'customer-incident-priority.store', 'uses' => 'CustomerIncidentPriorityController@store'));
        Route::post('customer-incident-mapping/store', array('as' => 'customer-incident-mapping.store', 'uses' => 'CustomerIncidentSubjectAllocationController@store'));
        Route::get('customer-incident-mapping/list/{id?}', array('as' => 'customer-incident-mapping.list', 'uses' => 'CustomerIncidentSubjectAllocationController@getList'));
        Route::get('customer-incident-mapping/single/{id}', array('as' => 'customer-incident-mapping.single', 'uses' => 'CustomerIncidentSubjectAllocationController@getSingle'));
        Route::get('customer-incident-mapping/destroy/{id}', array('as' => 'customer-incident-mapping.destroy', 'uses' => 'CustomerIncidentSubjectAllocationController@destroy'));
        Route::get('customer-incident-priority/test', array('as' => 'customer-incident-priority.test', 'uses' => 'CustomerIncidentSubjectAllocationController@test'));

        Route::post('customer-incident-recipient/save', array('as' => 'customer-incident-recipient.store', 'uses' => 'IncidentReportRecipientController@store'));
        Route::get('customer-incident-recipient/list/{id}', array('as' => 'customer-incident-recipient.list', 'uses' => 'IncidentReportRecipientController@list'));
        /* Customer Incident Subject Mapping - End */
    });

    /* Supervisor Panel Masters - End */

    /* Time Tracker Masters - Start */

    Route::group(['middleware' => ['permission:time_tracker_masters']], function () {

        /* Mobile Settings - Start */
        Route::get('mobilesettings', array('as' => 'mobilesettings', 'uses' => 'MobileAppSettingController@index'));
        Route::post('mobilesettings/store', array('as' => 'mobilesettings.store', 'uses' => 'MobileAppSettingController@storeMobileSettings'));

        /* Mobile Settings - End */

        /* Mobile Settings - Start */
        Route::get('mobilesettings', array('as' => 'mobilesettings', 'uses' => 'MobileAppSettingController@index'));
        Route::post('mobilesettings/store', array('as' => 'mobilesettings.store', 'uses' => 'MobileAppSettingController@storeMobileSettings'));
        Route::get('keymanagement/mobilesettings', array('as' => 'keymanagement.mobilesettings', 'uses' => 'KeyManagementMobileAppSettingController@index'));
        Route::post('keymanagement/mobilesettings/store', array('as' => 'keymanagement.mobilesettings.store', 'uses' => 'KeyManagementMobileAppSettingController@storeMobileSettings'));
        /* Mobile Settings - End */

        /* Spare Bonus Module Settings - Start */
        Route::get('spare-bonus-model-settings', array('as' => 'spare-bonus-model-settings', 'uses' => 'SpareBonusSettingController@index'));
        Route::post('spare-bonus-model-settings/store', array('as' => 'spare-bonus-model-settings.store', 'uses' => 'SpareBonusSettingController@storeBonusModelSettings'));
        /* Spare Bonus Module Settings  - End */

        /* Mobile Security Patrol Subject - Start */
        Route::name('mobile-security-patrol-subject')->get('mobile-security-patrol-subject', 'MobileSecurityPatrolSubjectController@index');
        Route::get('mobile-security-patrol-subject/list', array('as' => 'mobile-security-patrol-subject.list', 'uses' => 'MobileSecurityPatrolSubjectController@getList'));
        Route::get('mobile-security-patrol-subject/single/{id}', array('as' => 'mobile-security-patrol-subject.single', 'uses' => 'MobileSecurityPatrolSubjectController@getSingle'));
        Route::post('mobile-security-patrol-subject/store', array('as' => 'mobile-security-patrol-subject.store', 'uses' => 'MobileSecurityPatrolSubjectController@store'));

        /* Mobile Security Patrol Subject - End */

        /* Work hour type- Start */
        Route::name('work-hour-type')->get('work-hour-type', 'WorkHourTypeController@index');
        Route::get('work-hour-type/list', array('as' => 'work-hour-type.list', 'uses' => 'WorkHourTypeController@getList'));
        Route::get('work-hour-type/single/{id}', array('as' => 'work-hour-type.single', 'uses' => 'WorkHourTypeController@getSingle'));
        Route::post('work-hour-type/store', array('as' => 'work-hour-type.store', 'uses' => 'WorkHourTypeController@store'));

        /*Work hour type- End */

        /* Work hour customer- Start */
        Route::name('work-hour-customer')->get('work-hour-customer', 'WorkHourCustomerController@index');
        Route::get('work-hour-customer/list', array('as' => 'work-hour-customer.list', 'uses' => 'WorkHourCustomerController@getList'));
        Route::get('work-hour-customer/single/{id}', array('as' => 'work-hour-customer.single', 'uses' => 'WorkHourCustomerController@getSingle'));
        Route::post('work-hour-customer/store', array('as' => 'work-hour-customer.store', 'uses' => 'WorkHourCustomerController@store'));

        /*Work hour type- End */

        /* Qr Patrol Settings - Start */
        Route::get('qr-patrol-settings', array('as' => 'qr-patrol-settings', 'uses' => 'QrPatrolSettingController@index'));
        Route::post('qr-patrol-settings/store', array('as' => 'qr-patrol-settings.store', 'uses' => 'QrPatrolSettingController@store'));
        /* Qr Patrol Settings - End */

        /* Manual Threshold - Start */
        Route::name('payroll-settings')->get('payroll-settings', 'PayrollSettingsController@index');
        Route::post('payroll-settings/store', array('as' => 'payroll-settings.store', 'uses' => 'PayrollSettingsController@store'));
        /* Manual Threshold - End */
    });

    /* Time Tracker Masters - End */

    /* Taining And Learning Masters - Start */

    Route::group(['middleware' => ['permission:training_learn_lookups']], function () {

        /* Training Course Category - start */
        Route::name('course-category')->get('course-category', 'TrainingCategoryController@index');
        Route::get('course-category/list', array('as' => 'course-category.list', 'uses' => 'TrainingCategoryController@getList'));
        Route::get('course-category/single/{id}', array('as' => 'course-category.single', 'uses' => 'TrainingCategoryController@getSingle'));
        Route::post('course-category/store', array('as' => 'course-category.store', 'uses' => 'TrainingCategoryController@store'));

        /* Training Course Category - end */

        /* Training Course - start */
        Route::name('course')->get('course', 'TrainingCourseController@index');
        Route::get('course/list', array('as' => 'course.list', 'uses' => 'TrainingCourseController@getList'));
        Route::get('course/single/{id}', array('as' => 'course.single', 'uses' => 'TrainingCourseController@getSingle'));
        Route::post('course/store', array('as' => 'course.store', 'uses' => 'TrainingCourseController@store'));

        /* Training Course - end */

        /* Employee profile - start */
        Route::name('employee-profile')->get('employee-profile', 'TrainingEmployeeProfileController@index');
        Route::get('employee-profile/list', array('as' => 'employee-profile.list', 'uses' => 'TrainingEmployeeProfileController@getList'));
        Route::get('employee-profile/single/{id}', array('as' => 'employee-profile.single', 'uses' => 'TrainingEmployeeProfileController@getSingle'));
        Route::get('employee-profile/destroy/{id}', array('as' => 'employee-profile.destroy', 'uses' => 'TrainingEmployeeProfileController@destroy'));
        Route::post('employee-profile/store', array('as' => 'employee-profile.store', 'uses' => 'TrainingEmployeeProfileController@store'));

        /* Employee profile - end */

        /* Site profile - start */
        Route::name('site-profile')->get('site-profile', 'TrainingSiteProfileController@index');
        Route::get('site-profile/list', array('as' => 'site-profile.list', 'uses' => 'TrainingSiteProfileController@getList'));
        Route::get('site-profile/single/{id}', array('as' => 'site-profile.single', 'uses' => 'TrainingSiteProfileController@getSingle'));
        Route::get('site-profile/destroy/{id}', array('as' => 'site-profile.destroy', 'uses' => 'TrainingSiteProfileController@destroy'));
        Route::post('site-profile/store', array('as' => 'site-profile.store', 'uses' => 'TrainingSiteProfileController@store'));

        /* Site profile - end */

        /* Training Settings - start */
        Route::get('training-settings', array('as' => 'training-settings', 'uses' => 'TrainingSettingsController@index'));
        Route::post('training-settings/store', array('as' => 'training-settings.store', 'uses' => 'TrainingSettingsController@store'));
        /* Training Settings - end */
    });

    /* Taining And Learning Masters - End */

    /* Compliance Master - Start */

    Route::group(['middleware' => ['permission:compliance_lookups']], function () {

        /* Compliance Policy Category - start */
        Route::name('compliance-policy-category')->get('compliance-policy-category', 'CompliancePolicyCategoryController@index');
        Route::get('compliance-policy-category/list', array('as' => 'compliance-policy-category.list', 'uses' => 'CompliancePolicyCategoryController@getList'));
        Route::get('compliance-policy-category/single/{id}', array('as' => 'compliance-policy-category.single', 'uses' => 'CompliancePolicyCategoryController@getSingle'));
        Route::post('compliance-policy-category/store', array('as' => 'compliance-policy-category.store', 'uses' => 'CompliancePolicyCategoryController@store'));

        /* Compliance Policy Category Category - end */

        /* Compliance Policy - start */
        Route::name('policy')->get('policy', 'CompliancePolicyController@index');
        Route::get('policy/list', array('as' => 'policy.list', 'uses' => 'CompliancePolicyController@getList'));
        Route::get('policy/single/{id}', array('as' => 'policy.single', 'uses' => 'CompliancePolicyController@getSingle'));
        Route::post('policy/store', array('as' => 'policy.store', 'uses' => 'CompliancePolicyController@store'));
        Route::get('policy/analytcis', array('as' => 'policy.analytcis', 'uses' => 'CompliancePolicyController@analyticsIndex'));
        Route::post('policy/broadcast', array('as' => 'policy.broadcast', 'uses' => 'CompliancePolicyController@broadcastPolicy'));
        Route::get('policy/statistics', array('as' => 'policy.statistics', 'uses' => 'CompliancePolicyController@policyStatistics'));
        Route::post('policy/statistics/list', array('as' => 'policy-status.list', 'uses' => 'CompliancePolicyController@policyAllList'));

        /* Compliance Policy Category - end */
    });

    /* Compliance Master - End */

    /* Capacity Tool Master - Start */

    Route::group(['middleware' => ['permission:capacity_tool_lookups']], function () {

        /* Capacity Tool Area Lookup - Start */
        Route::name('area')->get('area', 'CapacityToolWorkClassificationAreaLookupController@index');
        Route::get('area/list', array('as' => 'area.list', 'uses' => 'CapacityToolWorkClassificationAreaLookupController@getList'));
        Route::get('area/single/{id}', array('as' => 'area.single', 'uses' => 'CapacityToolWorkClassificationAreaLookupController@getSingle'));
        Route::post('area/store', array('as' => 'area.store', 'uses' => 'CapacityToolWorkClassificationAreaLookupController@store'));

        /* Capacity Tool Area Lookup - End */

        /* Capacity Tool Task Frequency Lookup - Start */
        Route::name('task-frequency')->get('task-frequency', 'CapacityToolTaskFrequencyLookupController@index');
        Route::get('task-frequency/list', array('as' => 'task-frequency.list', 'uses' => 'CapacityToolTaskFrequencyLookupController@getList'));
        Route::get('task-frequency/single/{id}', array('as' => 'task-frequency.single', 'uses' => 'CapacityToolTaskFrequencyLookupController@getSingle'));
        Route::post('task-frequency/store', array('as' => 'task-frequency.store', 'uses' => 'CapacityToolTaskFrequencyLookupController@store'));

        /* Capacity Tool Task Frequency Lookup - End */

        /* Capacity Tool Status Lookup - Start */

        Route::name('status')->get('status', 'CapacityToolStatusLookupController@index');
        Route::get('status/list', array('as' => 'status.list', 'uses' => 'CapacityToolStatusLookupController@getList'));
        Route::get('status/single/{id}', array('as' => 'status.single', 'uses' => 'CapacityToolStatusLookupController@getSingle'));
        Route::post('status/store', array('as' => 'status.store', 'uses' => 'CapacityToolStatusLookupController@store'));
        /* Capacity Tool Status Lookup - End */

        /* Capacity Tool Objective Lookup - Start */

        Route::name('objective')->get('objective', 'CapacityToolObjectiveLookupController@index');
        Route::get('objective/list', array('as' => 'objective.list', 'uses' => 'CapacityToolObjectiveLookupController@getList'));
        Route::get('objective/single/{id}', array('as' => 'objective.single', 'uses' => 'CapacityToolObjectiveLookupController@getSingle'));
        Route::post('objective/store', array('as' => 'objective.store', 'uses' => 'CapacityToolObjectiveLookupController@store'));
        /* Capacity Tool Objective Lookup - End */

        /* Capacity Tool Skill Type Lookup - Start */
        Route::name('skill-type')->get('skill-type', 'CapacityToolSkillTypeLookupController@index');
        Route::get('skill-type/list', array('as' => 'skill-type.list', 'uses' => 'CapacityToolSkillTypeLookupController@getList'));
        Route::get('skill-type/single/{id}', array('as' => 'skill-type.single', 'uses' => 'CapacityToolSkillTypeLookupController@getSingle'));
        Route::post('skill-type/store', array('as' => 'skill-type.store', 'uses' => 'CapacityToolSkillTypeLookupController@store'));

        /* Capacity Tool Skill Type Lookup - End */
    });

    /* Capacity Tool Master - End */

    /* Client Masters - Start */
    Route::group(['middleware' => ['permission:client_lookups']], function () {

        /* Client Feedback Lookup - Start */
        Route::name('client-feedback')->get('client-feedback', 'ClientFeedbackLookupController@index');
        Route::get('client-feedback/list', array('as' => 'client-feedback.list', 'uses' => 'ClientFeedbackLookupController@getList'));
        Route::get('client-feedback/single/{id}', array('as' => 'client-feedback.single', 'uses' => 'ClientFeedbackLookupController@getSingle'));
        Route::post('client-feedback/store', array('as' => 'client-feedback.store', 'uses' => 'ClientFeedbackLookupController@store'));
        /* Client Feedback Lookup - End */

        /* Client Severity Lookup - Start */
        Route::name('severity')->get('severity', 'SeverityLookupController@index');
        Route::get('severity/list', array('as' => 'severity.list', 'uses' => 'SeverityLookupController@getList'));
        Route::get('severity/single/{id}', array('as' => 'severity.single', 'uses' => 'SeverityLookupController@getSingle'));
        Route::post('severity/store', array('as' => 'severity.store', 'uses' => 'SeverityLookupController@store'));
        /* Client Severity Lookup - End */

        /* Client Visitor Log Templates - Start */
        Route::get('visitorlog-templates', array('as' => 'visitorlog.templates', 'uses' => 'VisitorLogTemplateController@index'));
        Route::get('visitorlog-templates/list', array('as' => 'visitorlog-templates.list', 'uses' => 'VisitorLogTemplateController@getList'));
        Route::get('visitorlog-templates/add', array('as' => 'visitorlog-templates.add', 'uses' => 'VisitorLogTemplateController@addTemplate'));
        Route::post('visitorlog-templates/store', array('as' => 'visitorlog-templates.store', 'uses' => 'VisitorLogTemplateController@store'));
        Route::get('visitorlog-templates/update/{id}', array('as' => 'visitorlog-templates.update', 'uses' => 'VisitorLogTemplateController@addTemplate'));
        Route::get('visitorlog-templates/destroy', array('as' => 'visitorlog-templates.destroy', 'uses' => 'VisitorLogTemplateController@destroy'));
        /* Client Visitor Log Templates - End */

        /* Vistor-Log Template Allocation - start */
        Route::name('template-allocation')->get('template-allocation', 'VisitorLogTemplateAllocationController@index');
        Route::get('template-allocation/list/{customer_id?}', array('as' => 'template-allocation.list', 'uses' => 'VisitorLogTemplateAllocationController@getAllocationList'));
        Route::post('template-allocation/allocate', array('as' => 'template-allocation.allocate', 'uses' => 'VisitorLogTemplateAllocationController@allocate'));
        Route::post('template-allocation/unallocate', array('as' => 'template-allocation.unallocate', 'uses' => 'VisitorLogTemplateAllocationController@unallocate'));
        /* Vistor-Log Template Allocation  - end */

        /* Customer Terms And Condition Lookup - Start */
        Route::name('customer-terms-and-conditions')->get('customer-terms-and-conditions', 'CustomerTermsAndConditionController@index');
        Route::get('customer-terms-and-conditions/list', array('as' => 'customer-terms-and-conditions.list', 'uses' => 'CustomerTermsAndConditionController@getList'));
        Route::post('customer-terms-and-conditions/store', array('as' => 'customer-terms-and-conditions.store', 'uses' => 'CustomerTermsAndConditionController@store'));
        Route::put('customer-terms-and-conditions/update', array('as' => 'customer-terms-and-conditions.update', 'uses' => 'CustomerTermsAndConditionController@store'));
        Route::get('customer-terms-and-conditions/create', array('as' => 'customer-terms-and-conditions.create', 'uses' => 'CustomerTermsAndConditionController@create'));
        Route::get('customer-terms-and-conditions/destroy/{id}', array('as' => 'customer-terms-and-conditions.destroy', 'uses' => 'CustomerTermsAndConditionController@destroy'));
        Route::get('customer-terms-and-conditions/single/{id}', array('as' => 'customer-terms-and-conditions.single', 'uses' => 'CustomerTermsAndConditionController@single'));
        Route::get('customer-terms-and-conditions/edit/{id}', array('as' => 'customer-terms-and-conditions.edit', 'uses' => 'CustomerTermsAndConditionController@edit'));
        /* Customer Terms And Condition Lookup - End */

        /* Visitor Status Lookups Lookup - Start */
        Route::get('visitor-log-status-view', array('as' => 'visitor-log-status.view', 'uses' => 'VisitorStatusLookupsController@index'));
        Route::get('visitor-log-status-list', array('as' => 'visitor-log-status.list', 'uses' => 'VisitorStatusLookupsController@getList'));
        Route::post('visitor-log-status-list/store', array('as' => 'visitor-log-status-list.store', 'uses' => 'VisitorStatusLookupsController@store'));
        Route::get('visitor-log-status-list/single/{id}', array('as' => 'visitor-log-status-list.single', 'uses' => 'VisitorStatusLookupsController@getSingle'));
        Route::get('visitor-log-status-list/destroy/{id}', array('as' => 'visitor-log-status-list.destroy', 'uses' => 'VisitorStatusLookupsController@destroy'));
        /* Visitor Status Lookups Lookup - End */

        /* Visitor Status Lookups Lookup - Start */
        Route::get('work-hour-type/destroy/{id}', array('as' => 'work-hour-type.destroy', 'uses' => 'WorkHourTypeController@destroy'));
        Route::get('work-hour-customer/destroy/{id}', array('as' => 'work-hour-customer.destroy', 'uses' => 'WorkHourCustomerController@destroy'));
        Route::get('visitor-log/screening-templates', array('as' => 'visitor-log-screening-templates', 'uses' => 'VisitorLogScreeningTemplateController@index'));
        Route::get('visitor-log/screening-templates-list', array('as' => 'visitor-log-screening-templates-list', 'uses' => 'VisitorLogScreeningTemplateController@getList'));
        Route::post('visitor-log/screening-templates/store', array('as' => 'visitor-log-screening-templates.store', 'uses' => 'VisitorLogScreeningTemplateController@store'));
        Route::get('visitor-log/screening-templates/single/{id}', array('as' => 'visitor-log-screening-templates.single', 'uses' => 'VisitorLogScreeningTemplateController@getSingle'));
        Route::get('visitor-log/screening-templates/destroy/{id}', array('as' => 'visitor-log-screening-templates.destroy', 'uses' => 'VisitorLogScreeningTemplateController@destroy'));
        Route::get('visitor-log/screening-templates/office-allocation/destroy/{id}', array('as' => 'visitor-log-screening-templates.office-allocation.destroy', 'uses' => 'VisitorLogScreeningTemplateController@templatesOfficeAllocationDestroy'));

        Route::get('visitor-log/screening-templates/questions/{id}', array('as' => 'visitor-log-screening-templates.questions', 'uses' => 'VisitorLogScreeningTemplateQuestionController@index'));
        Route::get('visitor-log/screening-templates/questions-list/{id}', array('as' => 'visitor-log-screening-templates.questions-list', 'uses' => 'VisitorLogScreeningTemplateQuestionController@getList'));
        Route::post('visitor-log/screening-templates/questions/store', array('as' => 'visitor-log-screening-templates.questions.store', 'uses' => 'VisitorLogScreeningTemplateQuestionController@store'));
        Route::get('visitor-log/screening-templates/questions-single/{id}', array('as' => 'visitor-log-screening-templates.questions.single', 'uses' => 'VisitorLogScreeningTemplateQuestionController@getSingle'));
        Route::get('visitor-log/screening-templates/questions/destroy/{id}', array('as' => 'visitor-log-screening-templates.questions.destroy', 'uses' => 'VisitorLogScreeningTemplateQuestionController@destroy'));
    });

    /* Client Master - End */

    /* Document Masters - Start */

    Route::group(['middleware' => ['permission:document_lookups']], function () {

        /* document category detils */
        Route::name('document-category')->get('document-category', 'DocumentCategoryController@index');
        Route::get('document-category/list', array('as' => 'document-category.list', 'uses' => 'DocumentCategoryController@getList'));
        Route::get('document-category/single/{id}', array('as' => 'document-category.single', 'uses' => 'DocumentCategoryController@getSingle'));
        Route::post('document-category/store', array('as' => 'document-category.store', 'uses' => 'DocumentCategoryController@store'));

        /* document category detils */

        /* document name details */
        Route::name('document-name')->get('document-name', 'DocumentNameDetailController@index');
        Route::get('document-name/list', array('as' => 'document-name.list', 'uses' => 'DocumentNameDetailController@getList'));
        Route::get('document-name/single/{id}', array('as' => 'document-name.single', 'uses' => 'DocumentNameDetailController@getSingle'));
        Route::post('document-name/store', array('as' => 'document-name.store', 'uses' => 'DocumentNameDetailController@store'));
        Route::get('document-name/categorylist/{id}', array('as' => 'document-name.categorylist', 'uses' => 'DocumentNameDetailController@getCategoryList'));
        Route::get('document-name/othercategorynames/{id}', array('as' => 'document-name.othercategorynames', 'uses' => 'DocumentNameDetailController@getOtherCategoryNames'));

        /* document name details end */

        /* Other document category lookup Name -start */
        Route::name('other-document-category')->get('other-document-category', 'OtherCategoryLookupController@index');
        Route::get('other-document-category/list', array('as' => 'other-document-category.list', 'uses' => 'OtherCategoryLookupController@getList'));
        Route::get('other-document-category/single/{id}', array('as' => 'other-document-category.single', 'uses' => 'OtherCategoryLookupController@getSingle'));
        Route::post('other-document-category/store', array('as' => 'other-document-category.store', 'uses' => 'OtherCategoryLookupController@store'));

        /* Other document category lookup Name - End */

        /* Other category Name -start */

        Route::name('other-category')->get('other-category', 'OtherCategoryNameController@index');
        Route::get('other-category/list', array('as' => 'other-category.list', 'uses' => 'OtherCategoryNameController@getList'));
        Route::get('other-category/single/{id}', array('as' => 'other-category.single', 'uses' => 'OtherCategoryNameController@getSingle'));
        Route::post('other-category/store', array('as' => 'other-category.store', 'uses' => 'OtherCategoryNameController@store'));
        Route::get('other-category/destroy/{id}', array('as' => 'other-category.destroy', 'uses' => 'OtherCategoryNameController@destroy'));
        Route::get('other-category/categorylist/{id}', array('as' => 'other-category.categorylist', 'uses' => 'OtherCategoryNameController@getCategoryList'));

        /* Other category Name -end */
    });

    /* Document Masters - End */

    /* Supervisor Panel Masters - Start */

    // Route::group(['middleware' => ['permission:manage-masters']], function () {

    Route::group(['middleware' => ['permission:contractsadmin']], function () {
        /* Contracts - start */
        Route::get('contracts/view-submission-reason', array('as' => 'view-submission-reason.list', 'uses' => 'ContractsController@getSubmissionReasonList'));
        Route::get('submission-reason/list', array('as' => 'submission-reason.list', 'uses' => 'ContractsController@getReasonList'));
        Route::post('submission-reason/reasonforsubmissionupdate', array('as' => 'contracts.reasonforsubmissionupdate', 'uses' => 'ContractsController@reasonforsubmissionupdate'));
        Route::post('savesubmissionreason', array('as' => 'save-submission-reason', 'uses' => 'ContractsController@savesubmissionreason'));
        Route::post('updatesubmissionreason', array('as' => 'update-submission-reason', 'uses' => 'ContractsController@updatesubmissionreason'));
        Route::post('deletesubmissionreason', array('as' => 'delete-submission-reason', 'uses' => 'ContractsController@deletesubmissionreason'));

        /* Business Segment - start */
        Route::name('contracts/view-business-segment')->get('contracts/view-business-segment', 'ContractsBusinessSegmentController@index');
        Route::get('business-segment/list', array('as' => 'business-segment.list', 'uses' => 'ContractsBusinessSegmentController@getList'));
        Route::get('business-segment/single/{id}', array('as' => 'business-segment.single', 'uses' => 'ContractsBusinessSegmentController@getSingle'));
        Route::post('business-segment/store', array('as' => 'business-segment.store', 'uses' => 'ContractsBusinessSegmentController@store'));
        Route::get('business-segment/destroy/{id}', array('as' => 'business-segment.destroy', 'uses' => 'ContractsBusinessSegmentController@destroy'));
        /* Business Segment - end */
        /* Line of Business- start */
        Route::name('contracts/view-business-line')->get('contracts/view-business-line', 'ContractsBusinessLineController@index');
        Route::get('business-line/list', array('as' => 'business-line.list', 'uses' => 'ContractsBusinessLineController@getList'));
        Route::get('business-line/single/{id}', array('as' => 'business-line.single', 'uses' => 'ContractsBusinessLineController@getSingle'));
        Route::post('business-line/store', array('as' => 'business-line.store', 'uses' => 'ContractsBusinessLineController@store'));
        Route::get('business-line/destroy/{id}', array('as' => 'business-line.destroy', 'uses' => 'ContractsBusinessLineController@destroy'));
        /* Line of Business- end */
        /* Line of Division Look up- start */
        Route::name('contracts/view-division-lookup')->get('contracts/view-division-lookup', 'ContractsDivisionLookupController@index');
        Route::get('division-lookup/list', array('as' => 'division-lookup.list', 'uses' => 'ContractsDivisionLookupController@getList'));
        Route::get('division-lookup/single/{id}', array('as' => 'division-lookup.single', 'uses' => 'ContractsDivisionLookupController@getSingle'));
        Route::post('division-lookup/store', array('as' => 'division-lookup.store', 'uses' => 'ContractsDivisionLookupController@store'));
        Route::get('division-lookup/destroy/{id}', array('as' => 'division-lookup.destroy', 'uses' => 'ContractsDivisionLookupController@destroy'));
        /* Line of Division Look up- end */
        /* Payment allocatiom- start */
        Route::name('contracts/view-holiday-payment')->get('contracts/view-holiday-payment', 'ContractsHolidayPaymentController@index');
        Route::get('holiday-payment/list', array('as' => 'holiday-payment.list', 'uses' => 'ContractsHolidayPaymentController@getList'));
        Route::get('holiday-payment/single/{id}', array('as' => 'holiday-payment.single', 'uses' => 'ContractsHolidayPaymentController@getSingle'));
        Route::post('holiday-payment/store', array('as' => 'holiday-payment.store', 'uses' => 'ContractsHolidayPaymentController@store'));
        Route::get('holiday-payment/destroy/{id}', array('as' => 'holiday-payment.destroy', 'uses' => 'ContractsHolidayPaymentController@destroy'));
        /* Payment allocatiom- end */

        /* Contracts Billing Rate change - start */

        Route::get('contracts/view-billing-rate-changes', array('as' => 'view-billing-rate-changes.list', 'uses' => 'ContractBillingRateChangeController@index'));
        Route::get('view-billing-rate-changes/show', array('as' => 'view-billing-rate-changes.show', 'uses' => 'ContractBillingRateChangeController@show'));
        Route::post('saveratechangeperiod', array('as' => 'save-rate-change-period', 'uses' => 'ContractBillingRateChangeController@store'));
        Route::post('updateratechangeperiod', array('as' => 'update-rate-change-period', 'uses' => 'ContractBillingRateChangeController@store'));
        Route::post('deleteratechangeperiod', array('as' => 'delete-rate-change-period', 'uses' => 'ContractBillingRateChangeController@destroy'));
        /* Contracts - end */

        /* Contracts Billing cycle - start */
        Route::get('contracts/view-billing-cycle', array('as' => 'view-billing-cycle.index', 'uses' => 'ContractBillingCycleController@index'));
        Route::get('view-billing-cycle/show', array('as' => 'view-billing-cycle.show', 'uses' => 'ContractBillingCycleController@show'));
        Route::post('savebillingcycle', array('as' => 'save-billing-cycle', 'uses' => 'ContractBillingCycleController@store'));
        Route::post('updatebillingcycle', array('as' => 'update-billing-cycle', 'uses' => 'ContractBillingCycleController@store'));
        Route::post('deletebillingcycle', array('as' => 'delete-billing-cycle', 'uses' => 'ContractBillingCycleController@destroy'));
        /* Contracts Billing cycle - end */

        /* Contracts Payment methods - start */
        Route::get('contracts/view-payment-methods', array('as' => 'view-payment-methods.index', 'uses' => 'ContractPaymentMethodsController@index'));
        Route::get('view-payment-methods/show', array('as' => 'view-payment-methods.show', 'uses' => 'ContractPaymentMethodsController@show'));
        Route::post('savepaymentmethods', array('as' => 'save-payment-methods', 'uses' => 'ContractPaymentMethodsController@store'));
        Route::post('updatepaymentmethods', array('as' => 'update-payment-methods', 'uses' => 'ContractPaymentMethodsController@store'));
        Route::post('deletepaymentmethods', array('as' => 'delete-payment-methods', 'uses' => 'ContractPaymentMethodsController@destroy'));
        /* Contracts Payment methods - end */

        /* Contracts Device Access - start */
        Route::get('contracts/view-device-access', array('as' => 'view-device-access.index', 'uses' => 'ContractDeviceAccessController@index'));
        Route::get('view-device-access/show', array('as' => 'view-device-access.show', 'uses' => 'ContractDeviceAccessController@show'));
        Route::post('savedeviceaccess', array('as' => 'save-device-access', 'uses' => 'ContractDeviceAccessController@store'));
        Route::post('updatedeviceaccess', array('as' => 'update-device-access', 'uses' => 'ContractDeviceAccessController@store'));
        Route::post('deletedeviceaccess', array('as' => 'delete-device-access', 'uses' => 'ContractDeviceAccessController@destroy'));
        /* Contracts Device Access - end */

        /* Office Address Access - start */
        Route::get('contracts/view-office-address', array('as' => 'view-office-address.index', 'uses' => 'OfficeAddressController@index'));
        Route::get('view-office-address/show', array('as' => 'view-office-address.show', 'uses' => 'OfficeAddressController@show'));
        Route::post('saveofficeaddress', array('as' => 'save-office-address', 'uses' => 'OfficeAddressController@store'));
        Route::post('updateofficeaddress', array('as' => 'update-office-address', 'uses' => 'OfficeAddressController@store'));
        Route::post('deleteofficeaddress', array('as' => 'delete-office-address', 'uses' => 'OfficeAddressController@destroy'));
        /* Office Address Access - end */

        /* Contract cellphone provider - start */
        Route::get('contracts/view-cellphone-provider', array('as' => 'view-cellphone-provider.index', 'uses' => 'ContractCellphoneProviderController@index'));
        Route::get('view-cellphone-provider/show', array('as' => 'view-cellphone-provider.show', 'uses' => 'ContractCellphoneProviderController@show'));
        Route::post('savecellphoneprovider', array('as' => 'save-cellphone-provider', 'uses' => 'ContractCellphoneProviderController@store'));
        Route::post('updatecellphoneprovider', array('as' => 'update-cellphone-provider', 'uses' => 'ContractCellphoneProviderController@store'));
        Route::post('deletecellphoneprovider', array('as' => 'delete-cellphone-provider', 'uses' => 'ContractCellphoneProviderController@destroy'));
        /* Contract cellphone provider - end */

        /* Contracts Post Order Topics - start */
        Route::get('contracts/post-order-topics', array('as' => 'contracts.post-order-topics.index', 'uses' => 'PostOrderTopicController@index'));
        Route::get('contracts/post-order-topics/list', array('as' => 'contracts.post-order-topics.list', 'uses' => 'PostOrderTopicController@getList'));
        Route::post('contracts/post-order-topics/store', array('as' => 'contracts.post-order-topics.store', 'uses' => 'PostOrderTopicController@store'));
        Route::get('contracts/post-order-topics/single/{id}', array('as' => 'contracts.post-order-topics.single', 'uses' => 'PostOrderTopicController@getSingle'));
        Route::get('contracts/post-order-topics/destroy/{id}', array('as' => 'contracts.post-order-topics.destroy', 'uses' => 'PostOrderTopicController@destroy'));
        /* Contracts Post Order Topics - end */

        /* Contracts Post Order Groups start */
        Route::get('contracts/post-order-groups', array('as' => 'contracts.post-order-groups.index', 'uses' => 'PostOrderGroupController@index'));
        Route::get('contracts/post-order-groups/list', array('as' => 'contracts.post-order-groups.list', 'uses' => 'PostOrderGroupController@getList'));
        Route::post('contracts/post-order-groups/store', array('as' => 'contracts.post-order-groups.store', 'uses' => 'PostOrderGroupController@store'));
        Route::get('contracts/post-order-groups/single/{id}', array('as' => 'contracts.post-order-groups.single', 'uses' => 'PostOrderGroupController@getSingle'));
        Route::get('contracts/post-order-groups/destroy/{id}', array('as' => 'contracts.post-order-groups.destroy', 'uses' => 'PostOrderGroupController@destroy'));
        /* Contracts Post Order Groups - end */
    });

    // });
    /* Assignment Lookup - end */

    /* User Allocation - Start */
    Route::group(['middleware' => ['permission:employee-allocation']], function () {
        Route::name('allocation')->get('allocation', 'EmployeeAllocationController@index');
        Route::get('allocation/userlist/{role}', array('as' => 'allocation.userlist', 'uses' => 'EmployeeAllocationController@getUserLookup'));
        Route::post('allocation/allocate', array('as' => 'allocation.allocate', 'uses' => 'EmployeeAllocationController@allocate'));
        Route::get('allocation/guardallocate', array('as' => 'allocation.guardallocate', 'uses' => 'EmployeeAllocationController@guardallocate'));
        Route::get('allocation/list/{role?}/{supervisor_id?}', array('as' => 'allocation.list', 'uses' => 'EmployeeAllocationController@getAllocationList'));
        Route::post('allocation/unallocate', array('as' => 'allocation.unallocate', 'uses' => 'EmployeeAllocationController@unallocate'));
    });
    /* User Allocation - End */

    /* Settings - start */
    Route::name('settings.edit')->get('settings/mail', 'AdminController@settingsEdit');
    Route::name('settings.update')->put('settings/mail', 'AdminController@settingsUpdate');
    Route::name('settings.genericpwd')->get('settings/genericpwd', 'SettingsController@index');
    Route::name('settings.genericpwdstore')->post('settings/genericpwdstore', 'SettingsController@store');
    /* Settings - end */

    /* Email Settings - start */
    Route::name('email-accounts')->get('email-accounts', 'EmailAccountsController@index');
    Route::get('email-accounts/list', array('as' => 'email-accounts.list', 'uses' => 'EmailAccountsController@getList'));
    Route::post('email-accounts/store', array('as' => 'email-accounts.store', 'uses' => 'EmailAccountsController@store'));
    Route::get('email-accounts/single/{id}', array('as' => 'email-account.single', 'uses' => 'EmailAccountsController@getSingle'));
    Route::get('email-accounts/destroy/{id}', array('as' => 'email-account.destroy', 'uses' => 'EmailAccountsController@destroy'));
    /* Email Settings - end */



    /* Threshold Start */
    Route::group(['middleware' => ['permission:manage-schedule-threshold']], function () {
        Route::name('threshold')->get('threshold', 'ThresholdController@index');
        Route::post('threshold/store', array('as' => 'threshold.store', 'uses' => 'ThresholdController@store'));
    });
    /* Threshold End */

    /* User certificate expiry Start */
    Route::group(['middleware' => ['permission:manage-user-certificate-expiry-settings']], function () {
        Route::name('userCertificateExpirySettings')->get('user-certificate-expiry-settings', 'UserCertificateExpiryController@index');
        Route::name('userCertificateExpirySettings.store')->post('user-certificate-expiry-settings/store', array('uses' => 'UserCertificateExpiryController@store'));
        Route::get('user-certificate-expiry-settings/dueReminderMail', array('as' => 'user-certificate-expiry-settings.dueReminderMail', 'uses' => 'UserCertificateExpiryController@userCertificateExpiryDueReminderMail'));
    });
    /* User certificate expiry End */

    /* Roles and Permissions - start */

    Route::group(['middleware' => ['permission:manage-roles-permissions']], function () {
        Route::name('role')->get('role', 'RolesAndPermissionsController@index');
        Route::get('role/list', array('as' => 'role.list', 'uses' => 'RolesAndPermissionsController@getList'));
        Route::post('role/store', array('as' => 'role.store', 'uses' => 'RolesAndPermissionsController@store'));
        Route::get('role/update/{id?}', array('as' => 'role.update', 'uses' => 'RolesAndPermissionsController@addOrEdit'));
    });
    /* Roles and Permissions - end */

    Route::get('worktype/single/{id}', array('as' => 'worktype.single', 'uses' => 'WorkTypeController@getSingle'));

    Route::get('permission', array('as' => 'admin.permission', 'uses' => 'AdminController@createPermission'));
    Route::post('permission/store', array('as' => 'permission.store', 'uses' => 'AdminController@storePermission'));

    Route::group(['middleware' => ['permission:lookup-remove-entries']], function () {
        Route::get('user/destroy/{id}', array('as' => 'user.destroy', 'uses' => 'UserController@destroy'));
        Route::get('customer/destroy/{id}', array('as' => 'customer.destroy', 'uses' => 'CustomerController@destroy'));
        Route::get('parentcustomer/destroy/{id}', array('as' => 'parentcustomer.destroy', 'uses' => 'ParentCustomerController@destroy'));
        Route::get('position/destroy/{id}', array('as' => 'position.destroy', 'uses' => 'PositionLookupController@destroy'));
        Route::get('region/destroy/{id}', array('as' => 'region.destroy', 'uses' => 'RegionLookupController@destroy'));
        Route::get('industry-sector/destroy/{id}', array('as' => 'industry-sector.destroy', 'uses' => 'IndustrySectorLookupController@destroy'));
        Route::get('course-category/destroy/{id}', array('as' => 'course-category.destroy', 'uses' => 'TrainingCategoryController@destroy'));
        Route::get('course/destroy/{id}', array('as' => 'course.destroy', 'uses' => 'TrainingCourseController@destroy'));
        Route::get('candidate-assignment-type/destroy/{id}', array('as' => 'candidate-assignment-type.destroy', 'uses' => 'CandidateAssignmentTypeLookupController@destroy'));
        Route::get('rating-policy/destroy/{id}', array('as' => 'rating-policy.destroy', 'uses' => 'EmployeeRatingController@destroy'));

        Route::get('training/destroy/{id}', array('as' => 'training.destroy', 'uses' => 'TrainingLookupController@destroy'));
        Route::get('training-timing/destroy/{id}', array('as' => 'training-timing.destroy', 'uses' => 'TrainingTimingLookupController@destroy'));
        Route::get('criteria/destroy/{id}', array('as' => 'criteria.destroy', 'uses' => 'CriteriaLookupController@destroy'));
        Route::get('candidate-experience/destroy/{id}', array('as' => 'candidate-experience.destroy', 'uses' => 'CandidateExperienceLookupController@destroy'));
        Route::get('candidate-feedback-lookup/destroy/{id}', array('as' => 'candidate-feedback-lookup.destroy', 'uses' => 'FeedbackLookupController@destroy'));
        Route::get('tracking-lookup/destroy/{id}', array('as' => 'tracking-lookup.destroy', 'uses' => 'TrackingProcessLookupController@destroy'));
        Route::get('security-clearance/destroy/{id}', array('as' => 'security-clearance.destroy', 'uses' => 'SecurityClearanceLookupController@destroy'));
        Route::get('schedule-assignment-type/destroy/{id}', array('as' => 'schedule-assignment-type.destroy', 'uses' => 'ScheduleAssignmentTypeLookupController@destroy'));
        Route::get('payperiod/destroy/{id}', array('as' => 'payperiod.destroy', 'uses' => 'PayPeriodController@destroy'));
        Route::get('templatequestioncategory/destroy', array('as' => 'templatequestioncategory.destroy', 'uses' => 'TemplateQuestionsCategoryController@destroy'));
        Route::get('templates/destroy', array('as' => 'templates.destroy', 'uses' => 'TemplateController@destroy'));
        Route::get('worktype/destroy/{id}', array('as' => 'worktype.destroy', 'uses' => 'WorkTypeController@destroy'));
        Route::get('role/destroy/{id}', array('as' => 'role.destroy', 'uses' => 'RolesAndPermissionsController@destroy'));
        Route::get('holiday/destroy/{id}', array('as' => 'holiday.destroy', 'uses' => 'HolidayController@destroy'));
        Route::get('statholiday/destroy/{id}', array('as' => 'statholiday.destroy', 'uses' => 'HolidayController@statdestroy'));
        Route::get('other-document-category/destroy/{id}', array('as' => 'other-document-category.destroy', 'uses' => 'OtherCategoryLookupController@destroy'));
        Route::get('english-rating/destroy/{id}', array('as' => 'english-rating.destroy', 'uses' => 'EnglishRatingLookupController@destroy'));
        Route::get('employee-rating/destroy/{id}', array('as' => 'employee-rating.destroy', 'uses' => 'EmployeeRatingLookupController@destroy'));
        Route::get('compliance-policy-category/destroy/{id}', array('as' => 'compliance-policy-category.destroy', 'uses' => 'CompliancePolicyCategoryController@destroy'));
        Route::get('policy/destroy/{id}', array('as' => 'policy.destroy', 'uses' => 'CompliancePolicyController@destroy'));
        Route::get('incidentreportsubjects/destroy/{id}', array('as' => 'incidentreportsubjects.destroy', 'uses' => 'IncidentReportSubjectsController@destroy'));
        Route::get('leavereasons/destroy/{id}', array('as' => 'leavereasons.destroy', 'uses' => 'LeaveReasonController@destroy'));
        Route::get('smart-phone-type/destroy/{id}', array('as' => 'smart-phone-type.destroy', 'uses' => 'SmartPhoneTypeLookupController@destroy'));
        Route::get('candidate-termination-reason/destroy/{id}', array('as' => 'candidate-termination-reason.destroy', 'uses' => 'CandidateTerminationReasonLookupController@destroy'));
        Route::get('exit-resignation-reason/destroy/{id}', array('as' => 'exit-resignation-reason.destroy', 'uses' => 'ExitResignationReasonLookupController@destroy'));
        Route::get('employee-whistleblower-category/destroy/{id}', array('as' => 'employee-whistleblower-category.destroy', 'uses' => 'EmployeeWhistleblowerCategoryController@destroy'));
        Route::get('employee-whistleblower-priority/destroy/{id}', array('as' => 'employee-whistleblower-priority.destroy', 'uses' => 'EmployeeWhistleblowerPriorityController@destroy'));
        Route::get('time-off-request-type/destroy/{id}', array('as' => 'time-off-request-type.destroy', 'uses' => 'TimeOffRequestTypeLookupController@destroy'));
        Route::get('time-off-category/destroy/{id}', array('as' => 'time-off-category.destroy', 'uses' => 'TimeOffCategoryLookupController@destroy'));
        Route::get('operation-centre-email/destroy/{id}', array('as' => 'operation-centre-email.destroy', 'uses' => 'OperationCentreEmailController@destroy'));
        Route::get('candidate-brand-awareness/destroy/{id}', array('as' => 'candidate-brand-awareness.destroy', 'uses' => 'CandidateBrandAwarenessController@destroy'));
        Route::get('candidate-security-awareness/destroy/{id}', array('as' => 'candidate-security-awareness.destroy', 'uses' => 'CandidateSecurityAwarenessController@destroy'));
        Route::get('area/destroy/{id}', array('as' => 'area.destroy', 'uses' => 'CapacityToolWorkClassificationAreaLookupController@destroy'));
        Route::get('task-frequency/destroy/{id}', array('as' => 'task-frequency.destroy', 'uses' => 'CapacityToolTaskFrequencyLookupController@destroy'));
        Route::get('status/destroy/{id}', array('as' => 'status.destroy', 'uses' => 'CapacityToolStatusLookupController@destroy'));
        Route::get('objective/destroy/{id}', array('as' => 'objective.destroy', 'uses' => 'CapacityToolObjectiveLookupController@destroy'));
        Route::get('skill-type/destroy/{id}', array('as' => 'skill-type.destroy', 'uses' => 'CapacityToolSkillTypeLookupController@destroy'));
        Route::get('rolelookup/destroy/{id}', array('as' => 'rolelookup.destroy', 'uses' => 'RoleLookupController@destroy'));
        Route::get('banks/destroy/{id}', array('as' => 'banks.destroy', 'uses' => 'BanksController@destroy'));
        Route::get('salutation/destroy/{id}', array('as' => 'salutation.destroy', 'uses' => 'UserSalutationController@destroy'));
        //Competency
        Route::name('competency-matrix-category.destroy')->get('competency-matrix-category/{id}/destroy', 'CompetencyMatrixCategoryLookupController@destroy');
        Route::name('competency-matrix-rating.destroy')->get('competency-matrix-rating/{id}/destroy', 'CompetencyMatrixRatingLookupController@destroy');
        Route::name('competency-matrix.destroy')->get('competency-matrix/{id}/destroy', 'CompetencyMatrixLookupController@destroy');
        Route::name('rate-experiences.destroy')->get('rate-experiences/{id}/destroy', 'RateExperienceLookupController@destroy');
        Route::name('mobile-security-patrol-subject.destroy')->get('security-patrol-subject/{id}/destroy', 'MobileSecurityPatrolSubjectController@destroy');
        Route::name('client-feedback.destroy')->get('client-feedback/{id}/destroy', 'ClientFeedbackLookupController@destroy');
        Route::name('severity.destroy')->get('severity/{id}/destroy', 'SeverityLookupController@destroy');
        Route::name('sitestatus.destroy')->get('sitestatus/{id}/destroy', 'SiteNoteStatusLookupController@destroy');
        Route::name('user-certificate.destroy')->get('user-certificate/{id}/destroy', 'UserCertificateLookupController@destroy');
        //Commissionaires Understanding
        Route::get('commissionaires-understanding/destroy/{id}', array('as' => 'commissionaires-understanding.destroy', 'uses' => 'CommissionairesUnderstandingLookupController@destroy'));
        Route::get('document-name-detail/destroy/{id}', array('as' => 'document-name-detail.destroy', 'uses' => 'DocumentNameDetailController@destroy'));
        Route::get('document-category/destroy/{id}', array('as' => 'document-category.destroy', 'uses' => 'DocumentCategoryController@destroy'));
        Route::get('shift-module-dropdown/destroy/{id}', array('as' => 'shift-module-dropdown.destroy', 'uses' => 'ShiftModuleDropdownController@destroy'));
        Route::get('qrcode/destroy/{id}', array('as' => 'qrcode.destroy', 'uses' => 'CustomerQrCodeLocationController@destroy'));
        Route::get('employee-survey-template/destroy/{id}', array('as' => 'employee-survey-template.destroy', 'uses' => 'EmployeeSurveyTemplateController@destroy'));
        Route::get('user-emergency-contact-relation/destroy/{id}', array('as' => 'user-emergency-contact-relation.destroy', 'uses' => 'UserEmergencyContactRelationController@destroy'));
    });

    /* Dispatch Request Type start */
    Route::get('dispatch-request-types/list', array(
        'as' => 'dispatch-request-types.list',
        'uses' => 'MSTDispatchRequestTypeController@list',
    ));
    Route::resource('dispatch-request-types', 'MSTDispatchRequestTypeController');
    /* Dispatch Request Type end */

    /* Dispatch Coordinates Settings start */
    //Coordinate settings view
    Route::get('dispatch-coordinate-settings', array(
        'as' => 'dispatch_coordinate.settings',
        'uses' => 'MSTDispatchCoordinatesIdleSettingController@index',
    ));
    //coordinate settings details by id
    Route::get('dispatch_coordinate/settings/by/{id}', array(
        'as' => 'dispatch_coordinate.settings.show',
        'uses' => 'MSTDispatchCoordinatesIdleSettingController@show',
    ));
    //get all coordinates settings as json
    Route::get('dispatch_coordinate/settings/as/json', array(
        'as' => 'dispatch_coordinate.settings_api',
        'uses' => 'MSTDispatchCoordinatesIdleSettingController@getIdleSettings',
    ));
    //update the coordinate entity
    Route::post('dispatch_coordinate/settings/update', array(
        'as' => 'dispatch_coordinate.settings.update',
        'uses' => 'MSTDispatchCoordinatesIdleSettingController@update',
    ));
    /* Dispatch Coordinates Settings end */

    /* Push Notification Settings start */
    //Push Notification role settings view
    Route::get('push-notification-role-settings', array(
        'as' => 'push_notification.role_settings',
        'uses' => 'MSTDispatchPushNotificationRoleSettingsController@index',
    ));
    // push notification role setting list all json
    Route::get('push_notification/role_settings/list', array(
        'as' => 'push_notification.role_settings.list',
        'uses' => 'MSTDispatchPushNotificationRoleSettingsController@list',
    ));
    //get role data for allocation.
    Route::get('push_notification/role_settings/roles_for_allocation', array(
        'as' => 'push_notification.role_settings.roles_for_allocation',
        'uses' => 'MSTDispatchPushNotificationRoleSettingsController@roleDataForAllocation',
    ));
    //push notification remove allocated role
    Route::delete('push_notification/role_settings/{id}/destroy', array(
        'as' => 'push_notification.role_settings.destroy',
        'uses' => 'MSTDispatchPushNotificationRoleSettingsController@destroy',
    ));
    //attach a push notification role
    Route::post('push_notification/role_settings', array(
        'as' => 'push_notification.role_settings.store',
        'uses' => 'MSTDispatchPushNotificationRoleSettingsController@store',
    ));
    /* Push Notification Settings end */

    Route::name('rfp-tracking-process')->get('rfp/process-step', 'RfpTrackingProcessStepLookupController@index');
    Route::get('rfp-tracking-process/list', array('as' => 'rfp-tracking-process.list', 'uses' => 'RfpTrackingProcessStepLookupController@getList'));
    Route::post('rfp-tracking-process/store', array('as' => 'rfp-tracking-process.store', 'uses' => 'RfpTrackingProcessStepLookupController@store'));
    Route::get('rfp-tracking-process/single/{id}', array('as' => 'rfp-tracking-process.single', 'uses' => 'RfpTrackingProcessStepLookupController@getSingle'));
    Route::get('rfp-tracking-process/destroy/{id}', array('as' => 'rfp-tracking-process.destroy', 'uses' => 'RfpTrackingProcessStepLookupController@destroy'));
    Route::name('rfp-award-date')->get('rfp/award-date', 'RfpAwardDateLookupController@index');
    Route::get('rfp-award-date/list', array('as' => 'rfp-award-date.list', 'uses' => 'RfpAwardDateLookupController@getList'));
    Route::post('rfp-award-date/store', array('as' => 'rfp-award-date.store', 'uses' => 'RfpAwardDateLookupController@store'));
    Route::get('rfp-award-date/single/{id}', array('as' => 'rfp-award-date.single', 'uses' => 'RfpAwardDateLookupController@getSingle'));

    /* RFP response type lookup - start */
    Route::group(['prefix' => 'rfp'], function () {
        Route::get('response-type', array('as' => 'rfp-response-type', 'uses' => 'RfpResponseTypeLookupController@index'));
        Route::get('response-type/list', array('as' => 'rfp-response-type.list', 'uses' => 'RfpResponseTypeLookupController@getList'));
        Route::post('response-type/store', array('as' => 'rfp-response-type.store', 'uses' => 'RfpResponseTypeLookupController@store'));
        Route::get('response-type/destroy/{id}', array('as' => 'rfp-response-type.destroy', 'uses' => 'RfpResponseTypeLookupController@destroy'));
        Route::get('response-type/single/{id}', array('as' => 'rfp-response-type.single', 'uses' => 'RfpResponseTypeLookupController@getSingle'));
    });
    /* RFP response type lookup - end */

    /* RFP catalogue type lookup - start */
    Route::get('rfp-catalogue/group', array('as' => 'rfp-catalogue-group', 'uses' => 'RfpCatalogueGroupLookupController@index'));
    Route::get('rfp-catalogue-group/list', array('as' => 'rfp-group.list', 'uses' => 'RfpCatalogueGroupLookupController@getList'));
    Route::post('rfp-catalogue-group/store', array('as' => 'rfp-group.store', 'uses' => 'RfpCatalogueGroupLookupController@store'));
    Route::get('rfp-catalogue-group/single/{id}', array('as' => 'rfp-group.single', 'uses' => 'RfpCatalogueGroupLookupController@getSingle'));
    Route::get('rfp-catalogue-group/destroy/{id}', array('as' => 'rfp-group.destroy', 'uses' => 'RfpCatalogueGroupLookupController@destroy'));
    /* RFP catalogue type lookup - end */

    /* Client on-boarding settings - start */
    Route::get(
        'client-onboarding/settings',
        array(
            'as' => 'client-onboarding-settings',
            'uses' => 'ClientOnboardingSettingsController@index',
        )
    );
    Route::post(
        'client-onboarding/settings',
        array(
            'as' => 'client-onboarding-settings.store',
            'uses' => 'ClientOnboardingSettingsController@store',
        )
    );
    /* Client on-boarding settings - end */

    /* Client on-boarding template - start */
    Route::get(
        'client-onboarding/template',
        array(
            'as' => 'client-onboarding-template',
            'uses' => 'ClientOnboardingTemplateController@index',
        )
    );
    Route::get(
        'client-onboarding/template/list',
        array(
            'as' => 'client-onboarding-template.list',
            'uses' => 'ClientOnboardingTemplateController@getList',
        )
    );
    Route::post(
        'client-onboarding/template/store',
        array(
            'as' => 'client-onboarding-template.store',
            'uses' => 'ClientOnboardingTemplateController@store',
        )
    );
    Route::get(
        'client-onboarding/template/single/{id}',
        array(
            'as' => 'client-onboarding-template.single',
            'uses' => 'ClientOnboardingTemplateController@getSingle',
        )
    );
    Route::get(
        'client-onboarding/template/destroy/{id}',
        array(
            'as' => 'client-onboarding-template.destroy',
            'uses' => 'ClientOnboardingTemplateController@destroy',
        )
    );
    /* Client on-boarding template - end */

    /* timesheet approval configuration -start */
    Route::name('timesheet-configuration')->get('timesheet-configuration', 'TimesheetConfigurationController@index');
    Route::get('timesheet-configuration/list', array('as' => 'timesheet-configuration.list', 'uses' => 'TimesheetConfigurationController@getList'));
    Route::post('timesheet-configuration/store', array('as' => 'timesheet-configuration.store', 'uses' => 'TimesheetConfigurationController@store'));

    /* timesheet approval configuration -end */

    //template save
    Route::name('email-template')->get('email-template', 'ClientEmailTemplateController@index');
    Route::post('email-template/store', array('as' => 'email-template.store', 'uses' => 'ClientEmailTemplateController@store'));
    Route::get('email-template/single/{id}', array('as' => 'email-template.single', 'uses' => 'ClientEmailTemplateController@getSingle'));
    Route::get('email-template/helper/{id}', array('as' => 'email-template.helper', 'uses' => 'ClientEmailTemplateController@getHelpers'));

    //template save

    Route::name('email-template-allocation')->get('email-template-allocation/data/{type?}/{customer_id?}', 'ClientEmailTemplateController@allocation');

    Route::get('email-template-allocation/list', array('as' => 'allocation.emaillist', 'uses' => 'ClientEmailTemplateController@allocationList'));
    Route::get('email-template-allocation/datalist/{type_id?}/{customer_id?}', array('as' => 'email-template-allocation.list', 'uses' => 'ClientEmailTemplateController@allocationDatatableLoad'));

    Route::get('email-template-allocation/email', array('as' => 'allocation.email', 'uses' => 'ClientEmailTemplateController@getUsers'));
    Route::post('email-template-allocation/email/store', array('as' => 'email-template-allocation.store', 'uses' => 'ClientEmailTemplateController@storeAllocation'));
    Route::get('email-template-allocation/email/list', array('as' => 'email-template-allocation.allocated-users', 'uses' => 'ClientEmailTemplateController@getAllocatedUsers'));
    Route::get('email-template-allocation/users/list/{template_id}/{customer_id?}', array('as' => 'allocation.userslist', 'uses' => 'ClientEmailTemplateController@editAllocatedUsers'));
});

/* Site Settings - Start */
Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin', 'namespace' => 'Modules\Admin\Http\Controllers'], function () {
    Route::get('sitesettings', array('as' => 'sitesettings', 'uses' => 'SiteSettingsController@index'));
    Route::post('sitesettings/store', array('as' => 'sitesettings.store', 'uses' => 'SiteSettingsController@storeSiteSettings'));
});
/* Site Settings - End */
Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin', 'namespace' => 'Modules\Admin\Http\Controllers'], function () {
    Route::get('customer/customershifts', array('as' => 'customer.customershifts', 'uses' => 'CustomerController@getCustomershifts'));
});
/* Ids Scheduling - Start */
Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin', 'namespace' => 'Modules\Admin\Http\Controllers\IdsServices'], function () {
    //IDs Service management
    Route::get('idsServices', array('as' => 'idsServices', 'uses' => 'IdsServicesController@index'));
    Route::get('idsServices/getAll', array('as' => 'idsServices.getAll', 'uses' => 'IdsServicesController@getAll'));
    Route::post('idsServices/store', array('as' => 'idsServices.store', 'uses' => 'IdsServicesController@store'));
    Route::get('idsServices/single/{id}', array('as' => 'idsServices.single', 'uses' => 'IdsServicesController@getById'));
    Route::get('idsServices/destroy/{id}', array('as' => 'idsServices.destroy', 'uses' => 'IdsServicesController@destroy'));
    //IDs Office management
    Route::get('idsOffice', array('as' => 'idsOffice', 'uses' => 'IdsOfficeController@index'));
    Route::get('idsOffice/getAll', array('as' => 'idsOffice.getAll', 'uses' => 'IdsOfficeController@getAll'));
    Route::post('idsOffice/store', array('as' => 'idsOffice.store', 'uses' => 'IdsOfficeController@store'));
    Route::get('idsOffice/single/{id}', array('as' => 'idsOffice.single', 'uses' => 'IdsOfficeController@getById'));
    Route::get('idsOffice/destroy/{id}', array('as' => 'idsOffice.destroy', 'uses' => 'IdsOfficeController@destroy'));
    //IDs office time management
    Route::post('idsOffice/timing/store', array('as' => 'idsOffice.timing.store', 'uses' => 'IdsOfficeController@storeIdsTimings'));
    Route::post('idsOffice/timing/update', array('as' => 'idsOffice.timing.update', 'uses' => 'IdsOfficeController@updateIdsTimings'));
    Route::get('idsOffice/timing/destroy/{id}', array('as' => 'idsOffice.timing.destroy', 'uses' => 'IdsOfficeController@removeIdsTimings'));

    Route::get('payment-methods', array('as' => 'payment-methods', 'uses' => 'PaymentMethodsController@index'));
    Route::get('payment-methods/getAll', array('as' => 'payment-methods.getAll', 'uses' => 'PaymentMethodsController@getAll'));
    Route::post('payment-methods/store', array('as' => 'payment-methods.store', 'uses' => 'PaymentMethodsController@store'));
    Route::get('payment-methods/single/{id}', array('as' => 'payment-methods.single', 'uses' => 'PaymentMethodsController@getById'));
    Route::get('payment-methods/destroy/{id}', array('as' => 'payment-methods.destroy', 'uses' => 'PaymentMethodsController@destroy'));

    Route::get('payment-reasons', array('as' => 'payment-reasons', 'uses' => 'IdsPaymentReasonsController@index'));
    Route::get('payment-reasons/getAll', array('as' => 'payment-reasons.getAll', 'uses' => 'IdsPaymentReasonsController@getAll'));
    Route::post('payment-reasons/store', array('as' => 'payment-reasons.store', 'uses' => 'IdsPaymentReasonsController@store'));
    Route::get('payment-reasons/single/{id}', array('as' => 'payment-reasons.single', 'uses' => 'IdsPaymentReasonsController@getById'));
    Route::get('payment-reasons/destroy/{id}', array('as' => 'payment-reasons.destroy', 'uses' => 'IdsPaymentReasonsController@destroy'));

    Route::get('idsOffice/slots/{id}', array('as' => 'idsOffice.slot-page', 'uses' => 'IdsOfficeSlotController@getOfficeSlot'));
    Route::get('idsOffice/slots-block-page/{officeId}', array('as' => 'idsOffice.slot-block-page', 'uses' => 'IdsOfficeSlotController@getBlockedSlotPage'));
    Route::get('idsOffice/slots-block-data/{officeId}', array('as' => 'idsOffice.slot-block-data', 'uses' => 'IdsOfficeSlotController@getAllOfficeBlockedSlot'));
    Route::get('idsOffice/slots-block-data-search', array('as' => 'idsOffice.slot-block-data-search', 'uses' => 'IdsOfficeSlotController@getAllBlockedSlots'));
    Route::get('idsOffice/slots-data/{officeId}', array('as' => 'idsOffice.slot-data', 'uses' => 'IdsOfficeSlotController@getAllByOfficeId'));
    Route::post('idsOffice/slots-blocking', array('as' => 'idsOffice.slot.blocking', 'uses' => 'IdsOfficeSlotController@slotBlocking'));
    Route::get('idsOffice/slots-blocking/destroy/{id}', array('as' => 'idsOffice.slots-blocking.destroy', 'uses' => 'IdsOfficeSlotController@slotsBlockingDestroy'));
    Route::post('idsOffice/slots-block-data-remove', array('as' => 'idsOffice.slot-block-data-remove', 'uses' => 'IdsOfficeSlotController@destroyAllByIds'));

    /* Location(IDS Office) Allocation  - Start */
    Route::name('location-allocation')->get('location-allocation', 'LocationAllocationController@index');
    Route::get('location-allocation/list/{ids_location_id?}', array('as' => 'location-allocation.list', 'uses' => 'LocationAllocationController@getAllocationList'));
    Route::post('location-allocation/allocate', array('as' => 'location-allocation.allocate', 'uses' => 'LocationAllocationController@allocate'));
    Route::post('location-allocation/unallocate', array('as' => 'location-allocation.unallocate', 'uses' => 'LocationAllocationController@unallocate'));
    /* Location(IDS Office) Allocation  - End */

    /* Custom Questions - Start */
    Route::get(
        'custom-question',
        array(
            'as' => 'ids-custom-question',
            'uses' => 'IdsCustomQuestionController@index',
        )
    );
    Route::post(
        'custom-question/store',
        array(
            'as' => 'ids-custom-question.store',
            'uses' => 'IdsCustomQuestionController@store',
        )
    );
    Route::get(
        'custom-question-options-list/list',
        array(
            'as' => 'ids-custom-question-option-list.list',
            'uses' => 'IdsCustomQuestionController@getList',
        )
    );
    Route::get(
        'custom-question/edit/{id?}',
        array(
            'as' => 'ids-custom-question.edit',
            'uses' => 'IdsCustomQuestionController@getSingle',
        )
    );
    Route::get(
        'custom-question/destroy/{id}',
        array(
            'as' => 'ids-custom-question.destroy',
            'uses' => 'IdsCustomQuestionController@destroy',
        )
    );
    Route::get(
        'custom-question/option/list/{id?}',
        array(
            'as' => 'ids-custom-question-option.list',
            'uses' => 'IdsCustomQuestionController@getOptionList',
        )
    );
    Route::get(
        'custom-question/option/destroy/{id}',
        array(
            'as' => 'ids-custom-question-option.destroy',
            'uses' => 'IdsCustomQuestionController@destroyOption',
        )
    );
    Route::post(
        'custom-question/allocate',
        array(
            'as' => 'ids-custom-question.allocate',
            'uses' => 'IdsCustomQuestionController@allocate',
        )
    );
    Route::post(
        'custom-question/unallocate',
        array(
            'as' => 'ids-custom-question.unallocate',
            'uses' => 'IdsCustomQuestionController@unallocate',
        )
    );
    /* Custom Questions - End */

    /* No Show setting - Start */
    Route::get('ids-noshow-settings', array('as' => 'ids-noshow-settings', 'uses' => 'IdsNoshowSettingsController@index'));
    Route::post('ids-noshow-settings', array('as' => 'ids-noshow-settings.store', 'uses' => 'IdsNoshowSettingsController@store'));
    /* No Show setting - End */

    /* Passport Photo - Start */
    Route::get('ids-passport-photos', array('as' => 'ids.passport-photos', 'uses' => 'IdsPassportPhotosController@index'));
    Route::get('ids-passport-photos-list', array('as' => 'ids.passport-photos.list', 'uses' => 'IdsPassportPhotosController@getAll'));
    Route::post('ids-passport-photos', array('as' => 'ids.passport-photos.store', 'uses' => 'IdsPassportPhotosController@store'));
    Route::get('ids-passport-photos/single/{id}', array('as' => 'ids.passport-photos.single', 'uses' => 'IdsPassportPhotosController@getById'));
    Route::get('ids-passport-photos/destroy/{id}', array('as' => 'ids.passport-photos.destroy', 'uses' => 'IdsPassportPhotosController@destroy'));
    /* Passport Photo - End */
});
/* Ids Scheduling - End */
Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin/uniform-scheduling/', 'namespace' => 'Modules\Admin\Http\Controllers'], function () {
    Route::post('offices/store', array('as' => 'uniform-scheduling.offices.store', 'uses' => 'UniformSchedulingOfficesController@store'));
    Route::get('offices', array('as' => 'uniform-scheduling.offices', 'uses' => 'UniformSchedulingOfficesController@index'));
    Route::get('offices/single/{id}', array('as' => 'uniform-scheduling.office.single', 'uses' => 'UniformSchedulingOfficesController@getById'));
    Route::get('offices/list', array('as' => 'uniform-scheduling.office.lists', 'uses' => 'UniformSchedulingOfficesController@getAll'));
    Route::get('offices/timings/{officeId}', array('as' => 'uniform-scheduling.offices.timings', 'uses' => 'UniformSchedulingOfficesController@getTimings'));
    Route::post('offices/timings', array('as' => 'uniform-scheduling.offices.timings-store', 'uses' => 'UniformSchedulingOfficesController@storeTimings'));
    Route::post('offices/timing/update', array('as' => 'uniform-scheduling.offices.timings-update', 'uses' => 'UniformSchedulingOfficesController@updateTimings'));
    Route::get('offices/timing/destroy/{id}', array('as' => 'uniform-scheduling.offices.timings.destroy', 'uses' => 'UniformSchedulingOfficesController@removeTimings'));

    Route::post('offices/block/store', array('as' => 'uniform-scheduling.offices.block.store', 'uses' => 'UniformSchedulingOfficesBlockController@store'));
    Route::get('offices/block/destroy/{id}', array('as' => 'uniform-scheduling.offices.block.destroy', 'uses' => 'UniformSchedulingOfficesBlockController@destroy'));
    Route::post('offices/block/update', array('as' => 'uniform-scheduling.offices.block-update', 'uses' => 'UniformSchedulingOfficesBlockController@updateBlock'));
    /* Custom Questions - Start */
    Route::get(
        'custom-question',
        array(
            'as' => 'uniform-scheduling-custom-question',
            'uses' => 'UniformSchedulingCustomQuestionController@index',
        )
    );
    Route::post(
        'custom-question/store',
        array(
            'as' => 'uniform-scheduling-custom-question.store',
            'uses' => 'UniformSchedulingCustomQuestionController@store',
        )
    );
    Route::get(
        'custom-question-options-list/list',
        array(
            'as' => 'uniform-scheduling-custom-question-option-list.list',
            'uses' => 'UniformSchedulingCustomQuestionController@getList',
        )
    );
    Route::get(
        'custom-question/edit/{id?}',
        array(
            'as' => 'uniform-scheduling-custom-question.edit',
            'uses' => 'UniformSchedulingCustomQuestionController@getSingle',
        )
    );
    Route::get(
        'custom-question/destroy/{id}',
        array(
            'as' => 'uniform-scheduling-custom-question.destroy',
            'uses' => 'UniformSchedulingCustomQuestionController@destroy',
        )
    );
    Route::get(
        'custom-question/option/list/{id?}',
        array(
            'as' => 'uniform-scheduling-custom-question-option.list',
            'uses' => 'UniformSchedulingCustomQuestionController@getOptionList',
        )
    );
    Route::get(
        'custom-question/option/destroy/{id}',
        array(
            'as' => 'uniform-scheduling-custom-question-option.destroy',
            'uses' => 'UniformSchedulingCustomQuestionController@destroyOption',
        )
    );
    Route::post(
        'custom-question/allocate',
        array(
            'as' => 'uniform-scheduling-custom-question.allocate',
            'uses' => 'UniformSchedulingCustomQuestionController@allocate',
        )
    );
    Route::post(
        'custom-question/unallocate',
        array(
            'as' => 'uniform-scheduling-custom-question.unallocate',
            'uses' => 'UniformSchedulingCustomQuestionController@unallocate',
        )
    );
    /* Custom Questions - End */
});
/* Threshold Start */
Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin', 'namespace' => 'Modules\Admin\Http\Controllers'], function () {
    Route::name('landing_page.new_configuration_window')->get('landing_page_new_configuration_index', 'LandingPageController@index');
    Route::post('landing_page_widget_layout_details', array('as' => 'landing_page.getWidgetLayoutDetails', 'uses' => 'LandingPageController@getWidgetLayoutDetails'));
    Route::get('landing_page_custom_table_fields_by_module', array('as' => 'landing_page.getCustomTableFieldsByModule', 'uses' => 'LandingPageController@getCustomTableFieldsByModule'));
    Route::post('landing_page_save_tab_details', array('as' => 'landing_page.saveTabDetails', 'uses' => 'LandingPageController@saveTabDetails'));
    Route::post('landing_page_tab_active_status', array('as' => 'landing_page.saveTabActiveStatus', 'uses' => 'LandingPageController@saveTabActiveStatus'));
    Route::get('landing_page_widget_module_list', array('as' => 'landing_page.getAllWidgetModules', 'uses' => 'LandingPageController@getAllWidgetModules'));
    Route::post('landing_page_remove_tab', array('as' => 'landing_page.removeTab', 'uses' => 'LandingPageController@removeTab'));
});
/* Threshold End */

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin', 'namespace' => 'Modules\Admin\Http\Controllers'], function () {
    Route::get('recruitment_dashboard_index', array('as' => 'recruitment_dashboard.new_configuration_index', 'uses' => 'RecruitingAnalyticsConfigurationController@index'));
    Route::get('recruitment_dashboard_new', array('as' => 'recruitment_dashboard.new_configuration_new', 'uses' => 'RecruitingAnalyticsConfigurationController@add'));
    Route::post('recruitment_dashboard_widget_layout_details', array('as' => 'recruitment_dashboard.getWidgetLayoutDetails', 'uses' => 'RecruitingAnalyticsConfigurationController@getWidgetLayoutDetails'));
    Route::get('recruitment_dashboard_custom_table_fields_by_module', array('as' => 'recruitment_dashboard.getCustomTableFieldsByModule', 'uses' => 'RecruitingAnalyticsConfigurationController@getCustomTableFieldsByModule'));
    Route::post('recruitment_dashboard_save_tab_details', array('as' => 'recruitment_dashboard.saveTabDetails', 'uses' => 'RecruitingAnalyticsConfigurationController@saveTabDetails'));
    Route::post('recruitment_dashboard_tab_active_status', array('as' => 'recruitment_dashboard.saveTabActiveStatus', 'uses' => 'RecruitingAnalyticsConfigurationController@saveTabActiveStatus'));
    Route::get('recruitment_dashboard_widget_module_list', array('as' => 'recruitment_dashboard.getAllWidgetModules', 'uses' => 'RecruitingAnalyticsConfigurationController@getAllWidgetModules'));
    Route::post('recruitment_dashboard_remove_tab', array('as' => 'recruitment_dashboard.removeTab', 'uses' => 'RecruitingAnalyticsConfigurationController@removeTab'));
    Route::get('recruitment_dashboard_tabs_list', array('as' => 'recruitment_dashboard.getRecruitingAnalyticsDetails', 'uses' => 'RecruitingAnalyticsConfigurationController@getRecruitingAnalyticsDetails'));

    /// User payroll Groups
    Route::get('user-payroll-groups', array('as' => 'admin.upg.view', 'uses' => 'UserPayrollGroupController@index'));
    Route::get('user-payroll-groups/list', array('as' => 'admin.upg.list', 'uses' => 'UserPayrollGroupController@getList'));
    Route::post('user-payroll-groups/store', array('as' => 'admin.upg.store', 'uses' => 'UserPayrollGroupController@store'));
    Route::get('user-payroll-groups/single/{id}', array('as' => 'admin.upg.single', 'uses' => 'UserPayrollGroupController@getById'));
    Route::get('user-payroll-groups/destroy/{id}', array('as' => 'admin.upg.destroy', 'uses' => 'UserPayrollGroupController@destroy'));

    ///Marital Status
    Route::get('marital-status', array('as' => 'admin.marital.view', 'uses' => 'MaritalStatusController@index'));
    Route::get('marital-status/list', array('as' => 'admin.marital.list', 'uses' => 'MaritalStatusController@getList'));
    Route::post('marital-status/store', array('as' => 'admin.marital.store', 'uses' => 'MaritalStatusController@store'));
    Route::get('marital-status/single/{id}', array('as' => 'admin.marital.single', 'uses' => 'MaritalStatusController@getById'));
    Route::get('marital-status/destroy/{id}', array('as' => 'admin.marital.destroy', 'uses' => 'MaritalStatusController@destroy'));
});

Route::group([
    'middleware' => ['web', 'auth', 'permission:view_admin'], 'prefix' => 'admin',
    'namespace' => 'Modules\Admin\Http\Controllers',
], function () {

    //KPI master section
    Route::get('kpi/view', array('as' => 'admin.kpi.view', 'uses' => 'KPIMasterController@index'));
    Route::get('kpi/list', array('as' => 'admin.kpi.list', 'uses' => 'KPIMasterController@getList'));
    Route::post('kpi/store', array('as' => 'admin.kpi.store', 'uses' => 'KPIMasterController@store'));
    Route::get('kpi/single/{id}', array('as' => 'admin.kpi.single', 'uses' => 'KPIMasterController@getById'));
    Route::get('kpi/destroy/{id}', array('as' => 'admin.kpi.destroy', 'uses' => 'KPIMasterController@destroy'));

    //Header section
    Route::get('kpi/headers', array('as' => 'admin.kpi.headers', 'uses' => 'KpiCustomerHeaderController@index'));
    Route::post('kpi-header/store', array('as' => 'admin.kpi-header.store', 'uses' => 'KpiCustomerHeaderController@store'));
    Route::get('kpi-header/single/{id}', array('as' => 'admin.kpi-header.single', 'uses' => 'KpiCustomerHeaderController@getById'));
    Route::get('kpi-header/list', array('as' => 'admin.kpi-header.list', 'uses' => 'KpiCustomerHeaderController@getAll'));
    Route::delete('kpi-header/destroy/{id}', array('as' => 'admin.kpi-header.destroy', 'uses' => 'KpiCustomerHeaderController@destroy'));

    //Header section
    Route::get('kpi/customer/allocation/{customer_id}', array('as' => 'admin.kpi-customer-allocation.list', 'uses' => 'KpiMasterCustomerAllocationController@getAllByCustomerId'));
    Route::post('kpi/customer/allocation', array('as' => 'admin.kpi.customer.allocation', 'uses' => 'KpiMasterCustomerAllocationController@store'));
    Route::delete('kpi/customer/destroy/{id}', array('as' => 'admin.kpi-customer-allocation.destroy', 'uses' => 'KpiMasterCustomerAllocationController@destroy'));

    //Header kpi allocation
    Route::get('kpi/headers-kpi/allocation', array('as' => 'admin.kpi.header-allocation', 'uses' => 'KpiMasterAllocationController@index'));
    Route::get('kpi/customer-allocation/list', array('as' => 'admin.kpi.customer-allocation.list', 'uses' => 'KpiMasterAllocationController@getAll'));
    Route::get('kpi/customer-allocation/settings', array('as' => 'admin.kpi.customer-allocation.settings', 'uses' => 'KpiMasterAllocationController@getKpiAllocationSettings'));
    Route::get('kpi/customer-allocation/unset-list/{header_id?}', array('as' => 'admin.kpi.customer-allocation.unset-list', 'uses' => 'KpiMasterAllocationController@getUnallocatedKpis'));
    Route::post('kpi/customer-allocation/store', array('as' => 'admin.kpi.customer-allocation.store', 'uses' => 'KpiMasterAllocationController@store'));
    Route::delete('kpi/customer-allocation/destroy/{id}', array('as' => 'admin.kpi.customer-allocation.destroy', 'uses' => 'KpiMasterAllocationController@destroy'));
    Route::get('kpi/customer-allocation/single/{id}', array('as' => 'admin.kpi.customer-allocation.single', 'uses' => 'KpiMasterAllocationController@getById'));

    //Group section
    Route::get('kpi/groups/view', array('as' => 'admin.kpi.groups.view', 'uses' => 'KpiGroupController@index'));
    Route::get('kpi/groups/list', array('as' => 'admin.kpi.groups.list', 'uses' => 'KpiGroupController@getList'));
    Route::get('kpi/groups/leaf-nodes', array('as' => 'admin.kpi.groups.leaf-nodes', 'uses' => 'KpiGroupController@getAllLeafNodes'));
    Route::get('kpi/groups/parents', array('as' => 'admin.kpi.groups.parents', 'uses' => 'KpiGroupController@getAllParentNodes'));
    Route::post('kpi/groups/store', array('as' => 'admin.kpi.groups.store', 'uses' => 'KpiGroupController@store'));
    Route::get('kpi/groups/single/{id}', array('as' => 'admin.kpi.groups.single', 'uses' => 'KpiGroupController@getById'));
    Route::get('kpi/groups/destroy/{id}', array('as' => 'admin.kpi.groups.destroy', 'uses' => 'KpiGroupController@destroy'));

    //Group employee allocation
    Route::get('kpi/groups/allocation', array('as' => 'admin.kpi.groups-allocation', 'uses' => 'KpiGroupEmployeeAllocationController@index'));
    Route::get('kpi/groups/allocation/list', array('as' => 'admin.kpi.groups-allocation.list', 'uses' => 'KpiGroupEmployeeAllocationController@getAllocationList'));
    Route::post('kpi/groups/allocation/store', array('as' => 'admin.kpi.groups-allocation.store', 'uses' => 'KpiGroupEmployeeAllocationController@store'));
    Route::post('kpi/groups/allocation/unallocate', array('as' => 'admin.kpi.groups-allocation.unallocate', 'uses' => 'KpiGroupEmployeeAllocationController@unallocate'));

    //Customer group allocation
    Route::get('kpi/customer-group/list/{customer_id}', array('as' => 'admin.kpi.customer-group.list', 'uses' => 'KpiGroupCustomerAllocationController@getAllByCustomerId'));
    //Route::get('kpi/customer-allocation/settings/{customer_id}', array('as' => 'admin.kpi.customer-allocation.settings', 'uses' => 'KpiMasterAllocationController@getKpiAllocationSettings'));
    //Route::get('kpi/customer-group/unset-list/{header_id}', array('as' => 'admin.kpi.customer-allocation.unset-list', 'uses' => 'KpiMasterAllocationController@getUnallocatedKpis'));
    Route::post('kpi/customer-group/store', array('as' => 'admin.kpi.customer-group.store', 'uses' => 'KpiGroupCustomerAllocationController@store'));
    Route::delete('kpi/customer-group/destroy/{id}', array('as' => 'admin.kpi.customer-group.destroy', 'uses' => 'KpiGroupCustomerAllocationController@destroy'));
    Route::get('kpi/customer-group/single/{id}', array('as' => 'admin.kpi.customer-group.single', 'uses' => 'KpiGroupCustomerAllocationController@getById'));
});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin', 'namespace' => 'Modules\Admin\Http\Controllers'], function () {
    Route::get('stc_threshold_index', array('as' => 'stc_threshold.index', 'uses' => 'StcThresholdSettingsController@index'));
    Route::post('stc_threshold_store', array('as' => 'stc_threshold.store', 'uses' => 'StcThresholdSettingsController@store'));
    Route::get('stc_threshold_details', array('as' => 'stc_threshold.settings', 'uses' => 'StcThresholdSettingsController@getSettings'));
});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin', 'namespace' => 'Modules\Admin\Http\Controllers'], function () {
    Route::get('summary-dashboard', array('as' => 'admin.summary-dashboard-configuration', 'uses' => 'SummaryDashboardConfigurationController@index'));
    Route::post('summary-dashboard/store', array('as' => 'admin.summary-dashboard-configuration.store', 'uses' => 'SummaryDashboardConfigurationController@store'));
    Route::get('summary-dashboard/list', array('as' => 'admin.summary-dashboard-configuration.list', 'uses' => 'SummaryDashboardConfigurationController@getList'));
    Route::post('summary-dashboard/setconfiguration', array('as' => 'admin.dashboardConfiguration', 'uses' => 'SummaryDashboardConfigurationController@setDefaultConfiguration'));
});

