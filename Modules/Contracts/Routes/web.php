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


Route::group(['middleware' => ['web', 'permission:view_contracts', 'auth'], 'prefix' => 'contracts'], function () {
    /* Contracts CMUF Form - start */
    Route::post('cmuf-attachment-file', array('as' => 'contracts.attachfile', 'uses' => 'ContractsController@attachfile'));
    Route::post('cmuf-add-more-amendment-form', array('as' => 'contracts.addmoreamendmentsblock', 'uses' => 'ContractsController@addmoreamendmentview'));
    Route::post('cmuf-add-more-client-form', array('as' => 'contracts.addmoreclientblock', 'uses' => 'ContractsController@addmoreclientview'));
    Route::get('cmuf-upload-form', array('as' => 'contracts.cmuf-upload-form', 'uses' => 'ContractsController@uploadform'));
    Route::post('cmuf-upload-form-main', array('as' => 'upload.cmuf.attachmentmain', 'uses' => 'ContractsController@uploadformmainattachment'));
    Route::post('client-details', array('as' => 'contracts.clientdetails', 'uses' => 'ContractsController@getClientdetails'));
    Route::post('store', array('as' => 'contracts.storecontractform', 'uses' => 'ContractsController@store'));
    Route::post('get-user-details', array('as' => 'contracts.get-user-details', 'uses' => 'ContractsController@getUserdetails'));
    Route::get('view-cmuf', array('as' => 'contracts.previewcontract', 'uses' => 'ContractsController@viewContract'));
    Route::get('cmuf-filedownload', array('as' => 'contracts.downloadcontractattachment', 'uses' => 'ContractsController@downloadContractattachment'));
    Route::get('cmuf-all', array('as' => 'contracts.all-cmuf', 'uses' => 'ContractsController@viewAllContracts'));
    Route::get('cmuf-listcontracts', array('as' => 'contracts.contracts-list', 'uses' => 'ContractsController@getContractslist'));
    Route::get('edit-cmuf', array('as' => 'contracts.editcontract', 'uses' => 'ContractsController@editContract'));
    Route::get('edit-cmuf-form/{id}', array('as' => 'contracts.edit-contract-form', 'uses' => 'ContractsController@editContractForm'));
    Route::post('add-contract-amendments', array('as' => 'contracts.addContractamendment', 'uses' => 'ContractsController@addAmendments'));
    Route::get('get-amendments', array('as' => 'contracts.getAmendmentlist', 'uses' => 'ContractsController@getAmendmentlist'));
    Route::post('filter-cmuf-all', array('as' => 'contracts.filtercontractsummary', 'uses' => 'ContractsController@getFilteredContracts'));
    Route::post('removefile', array('as' => 'contracts.removefile', 'uses' => 'ContractsController@removeFile'));
    Route::post('removeAmendment', array('as' => 'contracts.removeAmendment', 'uses' => 'ContractsController@removeAmendment'));

    Route::post('postpagecmuf', array('as' => 'contracts.postcmuf', 'uses' => 'ContractsController@postcmuf'));
    Route::post('edit-cmuf-blocks', array('as' => 'contracts.editcontractblocks', 'uses' => 'ContractsController@editContractblocks'));
    Route::post('remove-cmuf-clients', array('as' => 'contracts.removeclient', 'uses' => 'ContractsController@removeContractclients'));
    Route::post('add-cmuf-clients', array('as' => 'contracts.addmoreclient', 'uses' => 'ContractsController@addContractclients'));


    /*Contracts Submission Reason master */
    Route::get('contracr-expiry/email', array('as' => 'contracts.expiry', 'uses' => 'ContractsController@contractExpiryEmailNotification'));
});

Route::group(['middleware' => ['web','auth'], 'prefix' => 'admin',], function () {

    Route::get('contract-expiry/settings',array('as' => 'client-onboarding-settings',
        'uses' => 'ContractExpirySettingsController@index'));
    Route::post('contract-expiry/store', array('as' => 'contract-expiry-settings.store',
        'uses' => 'ContractExpirySettingsController@store'));
});

// RFP module Start
Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'rfp'], function () {

    Route::group(['middleware' => ['permission:create_rfp']], function () {
        Route::get('create', array('as' => 'rfp.create', 'uses' => 'RfpController@create'));
    });
    Route::post('store', array('as' => 'rfp.store', 'uses' => 'RfpController@store'));
    Route::get('rfp-trash', array('as' => 'rfp.trash', 'uses' => 'RfpController@rfpdestroy'));
    Route::post('rfp-link', array('as' => 'rfp.rfplink', 'uses' => 'RfpController@rfpLink'));
    Route::get('/summary', array('as' => 'rfp.summary', 'uses' => 'RfpController@rfpCreate'));
    Route::group(['middleware' => ['permission:edit_rfp']], function () {
        Route::get('/edit/{id}', array('as' => 'rfp.edit', 'uses' => 'RfpController@edit'));
    });
    Route::get('rfp-summary/list', array('as' => 'rfp-summary.list', 'uses' => 'RfpController@getList'));
    Route::post('/status/store', array('as' => 'rfp-status.store', 'uses' => 'RfpController@storeStatus'));
    Route::get('/{rfp_id}/track', array('as' => 'rfp.track', 'uses' => 'RfpController@trackrfp'));
    Route::post('/{rfp_id}/track', array('as' => 'rfp.track-store', 'uses' => 'RfpController@trackRfpStore'));
    Route::post('/status-win-lose/store', array('as' => 'rfp-status-win-lose.store', 'uses' => 'RfpController@storeWinLoseStatus'));
    Route::get('/remove-rfp-tracking-step/{lookup_id}/{rfp_id}', array('as' => 'remove-rfp-tracking-step', 'uses' => 'RfpController@rfpTrackingRemove'));
    Route::get('rfp-summary/winlose', array('as' => 'rfp-summary.winlose', 'uses' => 'RfpController@getWinlosedetails'));


    Route::get(
        '/{rfp_id}/track-client-onboarding',
        array(
            'as' => 'rfp.track', 'uses' => 'RfpController@trackrfp',
            'middleware' => ['permission:view_assigned_client_onboarding_steps|view_all_client_onboarding_steps|update_client_onboarding_step_status']
        )
    );

    Route::get(
        '/{rfp_id}/create-client-onboarding/{onboard_id?}',
        array(
            'as' => 'rfp.create-client-onboarding',
            'uses' => 'RfpController@createClientOnboarding',
            'middleware' => ['permission:configure_client_onboarding_tracking']
        )
    );
    Route::post(
        '/{rfp_id}/store-client-onboarding/{onboard_id?}',
        array(
            'as' => 'rfp.store-client-onboarding',
            'uses' => 'RfpController@storeClientOnboarding',
            'middleware' => ['permission:configure_client_onboarding_tracking']
        )
    );
    Route::get(
        '/{rfp_id}/track-client-onboarding',
        array(
            'as' => 'rfp.track-client-onboarding',
            'uses' => 'RfpController@trackClientOnboarding',
            'middleware' => ['permission:view_assigned_client_onboarding_steps|view_all_client_onboarding_steps|update_client_onboarding_step_status']
        )
    );
    //    Route::post('/{rfp_id}/track-client-onboarding',
    //        array(
    //            'as' => 'rfp.track-client-onboarding-store',
    //            'uses' => 'RfpController@trackRfpStore',
    //            'middleware' => ['permission:view_assigned_client_onboarding_steps|view_all_client_onboarding_steps|update_client_onboarding_step_status']
    //        )
    //    );

});
// RFP module End

// Post order Module
Route::group(
    [
        'middleware' => ['web', 'auth'],
        'prefix' => 'post-order',
        
    ],
    function () {
        Route::group(['middleware' => ['permission:create_post_order|create_allocated_post_order']], function () {
            Route::get('create/{id?}', array('as' => 'post-order.create.view', 'uses' => 'PostOrderController@createView'));
            Route::post('create', array('as' => 'post-order.create', 'uses' => 'PostOrderController@create'));
        });
        Route::group(['middleware' => ['permission:view_post_order|view_allocated_post_order|create_post_order|create_allocated_post_order']], function () {
            Route::get('view', array('as' => 'post-order.view', 'uses' => 'PostOrderController@index'));
        });
        Route::get('post-order/list', array('as' => 'post-order.list', 'uses' => 'PostOrderController@getList'));
        Route::group(['middleware' => ['permission:approve_postorder']], function () {
            Route::post('update-status/{post_order_id}', array('as' => 'post-order.update-status', 'uses' => 'PostOrderController@changeStatus'));
        });
    }
);

// RFP Catalogue Module
Route::group(
    [
        'middleware' => ['web', 'auth'],
        'prefix' => 'rfp-catalogue',
        
    ],
    function () {

        Route::group(['middleware' => ['permission:create_rfp_catalogue']], function () {
            Route::get('create/{id?}', array('as' => 'rfp-catalogue.create.view', 'uses' => 'RfpCatalogueController@createView'));
            Route::post('create', array('as' => 'rfp-catalogue.create', 'uses' => 'RfpCatalogueController@create'));
        });
        Route::group(['middleware' => ['permission:view_rfp_catalogue|create_rfp_catalogue']], function () {
            Route::get('view', array('as' => 'rfp-catalogue.view', 'uses' => 'RfpCatalogueController@index'));
        });

        Route::get('rfp-catalogue/list', array('as' => 'rfp-catalogue.list', 'uses' => 'RfpCatalogueController@getList'));
        Route::group(['middleware' => ['permission:approve_rfp_catalog']], function () {
            Route::post('update-status/{post_order_id}', array('as' => 'rfp-catalogue.update-status', 'uses' => 'RfpCatalogueController@changeStatus'));
        });
    }
);
