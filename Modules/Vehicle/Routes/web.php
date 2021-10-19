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

Route::group([  'middleware' => ['web', 'auth', 'permission:view_admin'], 'prefix' => 'admin',], function()
{
    //Route::get('/', 'VehicleController@index');
    //Vehicle lists
    Route::name('vehicle-list')->get('vehicle-list', 'VehicleListController@index');
    Route::get('vehicle-list/list', array('as' => 'vehicle-list.list', 'uses' => 'VehicleListController@getList'));
    Route::post('vehicle-list/store', array('as' => 'vehicle-list.store', 'uses' => 'VehicleListController@store'));
    Route::get('vehicle-list/single/{id}', array('as' => 'vehicle-list.single', 'uses' => 'VehicleListController@getSingle'));
    Route::get('vehicle-list/destroy/{id}', array('as' => 'vehicle-list.destroy', 'uses' => 'VehicleListController@destroy'));

    Route::name('vehicle-maintenance-category')->get('vehicle-maintenance-category', 'VehicleMaintenanceCategoryController@index');
    Route::get('vehicle-maintenance-category/list', array('as' => 'vehicle-maintenance-category.list', 'uses' => 'VehicleMaintenanceCategoryController@getList'));
    Route::post('vehicle-maintenance-category/store', array('as' => 'vehicle-maintenance-category.store', 'uses' => 'VehicleMaintenanceCategoryController@store'));
    Route::get('vehicle-maintenance-category/single/{id}', array('as' => 'vehicle-maintenance-category.single', 'uses' => 'VehicleMaintenanceCategoryController@getSingle'));
    Route::get('vehicle-maintenance-category/destroy/{id}', array('as' => 'vehicle-maintenance-category.destroy', 'uses' => 'VehicleMaintenanceCategoryController@destroy'));


    Route::name('vehicle-maintenance-type')->get('vehicle-maintenance-type', 'VehicleMaintenanceTypeController@index');
    Route::get('vehicle-maintenance-type/list', array('as' => 'vehicle-maintenance-type.list', 'uses' => 'VehicleMaintenanceTypeController@getList'));
    Route::post('vehicle-maintenance-type/store', array('as' => 'vehicle-maintenance-type.store', 'uses' => 'VehicleMaintenanceTypeController@store'));
    Route::get('vehicle-maintenance-type/single/{id}', array('as' => 'vehicle-maintenance-type.single', 'uses' => 'VehicleMaintenanceTypeController@getSingle'));
    Route::get('vehicle-maintenance-type/destroy/{id}', array('as' => 'vehicle-maintenance-type.destroy', 'uses' => 'VehicleMaintenanceTypeController@destroy'));

    Route::name('vehicle-vendor-lookup')->get('vehicle-vendor-lookup', 'VehicleVendorLookupController@index');
    Route::get('vehicle-vendor-lookup/list', array('as' => 'vehicle-vendor-lookup.list', 'uses' => 'VehicleVendorLookupController@getList'));
    Route::post('vehicle-vendor-lookup/store', array('as' => 'vehicle-vendor-lookup.store', 'uses' => 'VehicleVendorLookupController@store'));
    Route::get('vehicle-vendor-lookup/single/{id}', array('as' => 'vehicle-vendor-lookup.single', 'uses' => 'VehicleVendorLookupController@getSingle'));
    Route::get('vehicle-vendor-lookup/destroy/{id}', array('as' => 'vehicle-vendor-lookup.destroy', 'uses' => 'VehicleVendorLookupController@destroy'));

});


Route::group([  'middleware' => ['web', 'auth','permission:view_vehicle']], function()
{

    Route::middleware(['permission:initiate_vehicle|edit_initiated_vehicle'])->group(function () {
        Route::get('vehicle/initiate', array('as' => 'vehicle.initiate', 'uses' => 'VehicleController@index'));
        Route::post('vehicle/initiate/store', array('as' => 'vehicle.initiate-store', 'uses' => 'VehicleController@store'));
        Route::get('vehicle/editVehicleInitiate/{id}', array('as' => 'vehicle.editVehicleInitiate', 'uses' => 'VehicleController@editVehicleInitiate'));
        Route::get('vehicle/getTypeDetails/{id}', array('as' => 'vehicle.getTypeDetails', 'uses' => 'VehicleController@getTypeDetails'));
    });

    Route::middleware(['permission:initiate_vehicle'])->group(function () {
        Route::get('vehicle/getTypeDetails/{id}', array('as' => 'vehicle.getTypeDetails', 'uses' => 'VehicleController@getTypeDetails'));
    });
    Route::middleware(['permission:edit_initiated_vehicle'])->group(function () {
        Route::get('vehicle/editVehicleInitiate/{id}', array('as' => 'vehicle.editVehicleInitiate', 'uses' => 'VehicleController@editVehicleInitiate'));


        Route::get('vehicle/getVehicleName/{id}', array('as' => 'vehicle.getVehicleName', 'uses' => 'VehicleController@getVehicleName'));
    });
    Route::middleware(['permission:add_maintanence_vehicle|view_vehicle_cumilative_km|view_completed_maintenance|view_pending_maintenance'])->group(function ()
    {
        Route::get('vehicle/maintenance', array('as' => 'vehicle.maintenance', 'uses' => 'VehicleMaintenanceController@index'));
        Route::get('vehicle/maintenance/list', array('as' => 'vehicle.maintenance.list', 'uses' => 'VehicleMaintenanceController@getList'));
        Route::post('vehicle/maintenance/store', array('as' => 'vehicle.maintenance.store', 'uses' => 'VehicleMaintenanceController@store'));
        Route::get('vehicle/pending/maintenance', array('as' => 'vehicle.pending.maintenance', 'uses' => 'VehiclePendingMaintenanceController@index'
        ));
        Route::get('vehicle/initiatedservice/typelist/{id}', array('as' => 'vehicle.getInitiatedServiceType', 'uses' => 'VehicleController@getInitiatedServiceType'));
        Route::get('vehicle/pending/maintenance/list/{all}', array('as' => 'vehicle.pending.maintenance.list', 'uses' => 'VehiclePendingMaintenanceController@getList'));
        Route::get('vehicle/pending/maintenance/single/{id}', array('as' => 'vehicle.pending.maintenance.single', 'uses' => 'VehiclePendingMaintenanceController@getSingle'));
        Route::get('vehicle/cumilative_km', array('as' => 'vehicle.cumilative_km', 'uses' => 'VehicleTripController@index'));
        Route::get('vehicle/cumilative/list', array('as' => 'vehicle.cumilative.list', 'uses' => 'VehicleTripController@getCumilativeKilometreList'));
        Route::get('vehicle/cumilative/single/{id}', array('as' => 'vehicle.cumilative.single', 'uses' => 'VehicleTripController@getSingle'));
        Route::get('vehicle/pending/maintenance/test', array('as' => 'vehicle.pending.maintenance.test', 'uses' => 'VehiclePendingMaintenanceController@test'));
        Route::get('vehicle/pending/maintenance/getsubtotal/{total}/{tax}', array('as' => 'vehicle.pending.maintenance.getsubtotal', 'uses' => 'VehiclePendingMaintenanceController@getSubtotal'));
    });
    Route::middleware(['permission:view_vehicle_analysis'])->group(function () {
        Route::get('vehicle/analysis', array('as' => 'vehicle.analysis', 'uses' => 'VehicleAnalysisController@index'));
        Route::get('vehicle/analysis/list', array('as' => 'vehicle.analysis.list', 'uses' => 'VehicleAnalysisController@getList'));
    });
});
