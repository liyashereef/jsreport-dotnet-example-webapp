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


Route::group(
    [
        'middleware' => ['web', 'auth'],
        'prefix' => 'ip-camera',
        'namespace' => 'Modules\IpCamera\Http\Controllers'
    ],
    function () {
        Route::get('/', array('as' => 'ip_camera.widget_view', 'uses' => 'IpCameraController@index'));
        Route::get('tabs', array('as' => 'ip-camera.tabs', 'uses' => 'IpCameraController@getDashboardTabs'));
        Route::get('tab-details', array('as' => 'ip-camera.tab-details', 'uses' => 'IpCameraController@getDashboardTabDetails'));
    }
);



Route::group(
    [
        'middleware' => ['web', 'auth'],
        'prefix' => 'admin/ip-camera',
        'namespace' => 'Modules\IpCamera\Http\Controllers\Admin'
    ],
    function () {
        Route::get('view/{id?}', array('as' => 'ip_camera.view', 'uses' => 'IpCameraMasterController@index'));
        Route::get('list/{id?}', array('as' => 'ip_camera.list', 'uses' => 'IpCameraMasterController@getList'));
        Route::post('store', array('as' => 'ip_camera.store', 'uses' => 'IpCameraMasterController@store'));
        Route::get('single/{id}', array('as' => 'ip_camera.single', 'uses' => 'IpCameraMasterController@getSingle'));
        Route::get('remove/{id}', array('as' => 'ip_camera.destroy', 'uses' => 'IpCameraMasterController@destroy'));

        //ipcamera configuration
        Route::get('ip-camera-dashboard-index', array('as' => 'ip-camera-dashboard.new-configuration-index', 'uses' => 'IpCameraWidgetConfigurationController@index'));
        Route::get('ip-camera-dashboard-tabs-list', array('as' => 'ip-camera-dashboard.getRecruitingAnalyticsDetails', 'uses' => 'IpCameraWidgetConfigurationController@getIpCameraConfigurationDetails'));
        Route::get('ip-camera-dashboard-new', array('as' => 'ip-camera-dashboard.new_configuration_new', 'uses' => 'IpCameraWidgetConfigurationController@add'));
        Route::post('ip-camera-dashboard-widget-layout-details', array('as' => 'ip-camera-dashboard.getWidgetLayoutDetails', 'uses' => 'IpCameraWidgetConfigurationController@getWidgetLayoutDetails'));
        Route::post('ip-camera-dashboard-widget-save-tab-details', array('as' => 'ip-camera-dashboard.saveTabDetails', 'uses' => 'IpCameraWidgetConfigurationController@saveTabDetails'));
        Route::post('ip-camera-dashboard-remove-tab', array('as' => 'ip-camera-dashboard.removeTab', 'uses' => 'IpCameraWidgetConfigurationController@removeTab'));
        Route::post('ip-camera-dashboard-tab-active-status', array('as' => 'ip-camera-dashboard.saveTabActiveStatus', 'uses' => 'IpCameraWidgetConfigurationController@saveTabActiveStatus'));
        Route::get('ip-camera-dashboard-custom-table-fields-by-module', array('as' => 'ip-camera-dashboard.getCustomTableFieldsByModule', 'uses' => 'IpCameraWidgetConfigurationController@getCustomTableFieldsByModule'));
        Route::post('cameraToken', array('as' => 'ip_camera.cameratoken', 'uses' => 'IpCameraMasterController@getCameraToken'));
    }
);
