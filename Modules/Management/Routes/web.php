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

Route::group(['middleware' => ['web','auth','permission:user_view'],
    'prefix' => 'management',], function () {
    Route::get('user-list', array('as' => 'management.userList', 'uses' => 'UserViewController@getList'));
    Route::get('user-list/view', array('as' => 'management.userViewList', 'uses' => 'UserViewController@getListView'));
    Route::get(
        'user-list/detail/{id?}',
        array('as' => 'management.userViewMore', 'uses' => 'UserViewController@getDetailView')
    );

    Route::middleware(['permission:user_tab_edit'])->group(function () {
        Route::post(
            'user-list/store-user/{id}',
            array('as' => 'management.userTabStore', 'uses' => 'UserViewController@userTabStore')
        );
    });
    Route::middleware(['permission:user_profile_edit'])->group(function () {
        Route::post(
            'user-list/store-user-profile/{id}',
            array('as' => 'management.profileTabStore', 'uses' => 'UserViewController@profileTabStore')
        );
    });
    Route::middleware(['permission:user_expense_edit'])->group(function () {
        Route::post(
            'user-list/store-user-expense/{id}',
            array('as' => 'management.expenseTabStore', 'uses' => 'UserViewController@expenseTabStore')
        );
    });
    Route::middleware(['permission:security_clearance_edit'])->group(function () {
        Route::post(
            'user-list/store-security-clearance/{id}',
            array('as' => 'management.securityClearanceTabStore', 'uses' => 'UserViewController@userSecurityClearanceStore')
        );
    });
    Route::middleware(['permission:user_certificates_edit'])->group(function () {
        Route::post(
            'user-list/store-certificate/{id}',
            array('as' => 'management.userCertificateStore', 'uses' => 'UserViewController@userCertificateStore')
        );
    });
    Route::middleware(['permission:user_skill_edit'])->group(function () {
        Route::post(
            'user-list/store-skill/{id}',
            array('as' => 'management.userSkillStore', 'uses' => 'UserViewController@userSkillStore')
        );
    });
});

Route::group(['middleware' => ['web','auth','permission:customer_view'],
    'prefix' => 'management',], function () {
    Route::get('customer-list', array('as' => 'management.customerList', 'uses' => 'CustomerViewController@getList'));
    Route::get(
        'customer-list/view',
        array('as' => 'management.customerViewList', 'uses' => 'CustomerViewController@getCustomerList')
    );
    Route::get(
        'customer-list/detail/{id?}',
        array('as' => 'management.customerViewMore', 'uses' => 'CustomerViewController@getDetailView')
    );
    Route::get('managementCustomer/getLandingPageDetails', array('as' => 'managementCustomer.getLandingPageDetails',
        'uses' => 'CustomerViewController@getLandingPageDetails'));
    Route::get('managementCustomer/allocatteduseremail/{userid?}', array('as' => 'managementCustomer.allocatteduseremail',
        'uses' => 'CustomerViewController@getAllocatedUserEmail'));

    Route::middleware(['permission:customer_profile_tab_edit'])->group(function () {
        Route::post(
            'customer-profile/store/{id}',
            array('as' => 'management.customerProfileStore', 'uses' => 'CustomerViewController@customerProfileStore')
        );
        Route::post('managementCustomer/reset_incident_logo', array('as' => 'managementCustomer.reset_incident_logo',
            'uses' => 'CustomerViewController@resetIncidentLogo'));
    });
    Route::middleware(['permission:cpid_allocation_edit'])->group(function () {
        Route::post(
            'customer-cpid/store/{id}',
            array('as' => 'management.customerCpidStore', 'uses' => 'CustomerViewController@customerCPIDStore')
        );
    });
    Route::middleware(['permission:preference_tab_edit'])->group(function () {
        Route::post(
            'customer-preference/store/{id}',
            array('as' => 'management.customerPreferenceStore', 'uses' => 'CustomerViewController@customerPreferenceStore')
        );
    });
    Route::middleware(['permission:fence_tab_edit'])->group(function () {
        Route::post(
            'customer-fence/store/{id}',
            array('as' => 'management.customerFenceStore', 'uses' => 'CustomerViewController@fenceStore')
        );
    });

    Route::middleware(['permission:qrcode_location_edit'])->group(function () {
        Route::get(
            'qrcode/getAll/{id?}',
            array('as' => 'management.qrcodeGetAll', 'uses' => 'CustomerViewController@getQRcodeDetails')
        );
        Route::get(
            'qrcode/single/{id}',
            array('as' => 'management.qrcodeSingle', 'uses' => 'CustomerViewController@getSingle')
        );
        Route::post(
            'qrcode/store',
            array('as' => 'management.qrcodeStore', 'uses' => 'CustomerViewController@qrCodeLocationStore')
        );
        Route::get(
            'qrcode/destroy/{id}',
            array('as' => 'management.qrcodeDestroy', 'uses' => 'CustomerViewController@destroy')
        );
    });


    Route::middleware(['permission:incident_subject_edit'])->group(function () {
        Route::get(
            'customer-incident-priority/check/{id}',
            array('as' => 'management.customerIncidentPriorityCheck', 'uses' => 'CustomerViewController@checkPriority')
        );
        Route::post(
            'customer-incident-priority/store',
            array('as' => 'management.customerIncidentPriorityStore', 'uses' => 'CustomerViewController@storeIncident')
        );
        Route::post(
            'customer-incident-mapping/store',
            array('as' => 'management.customerIncidentMappingStore', 'uses' => 'CustomerViewController@storeIncidentAllocation')
        );
        Route::get(
            'customer-incident-mapping/list/{id?}',
            array('as' => 'management.customerIncidentMappingList', 'uses' => 'CustomerViewController@getIncidentList')
        );
        Route::get(
            'customer-incident-mapping/single/{id}',
            array('as' => 'management.customerIncidentMappingSingle', 'uses' => 'CustomerViewController@getSingleIncident')
        );
        Route::get(
            'customer-incident-mapping/destroy/{id}',
            array('as' => 'management.customerIncidentMappingDestroy', 'uses' => 'CustomerViewController@destroyIncident')
        );
        Route::post(
            'customer-incident-recipient/save',
            array('as' => 'management.customerIncidentRecipientStore','uses' => 'CustomerViewController@storeIncidentRecipient')
        );
        Route::get(
            'customer-incident-recipient/list/{id}',
            array('as' => 'management.customerIncidentRecipientList','uses' => 'CustomerViewController@listIncidentRecipient')
        );
    });
});

