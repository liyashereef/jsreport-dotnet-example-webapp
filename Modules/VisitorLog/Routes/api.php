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

// Route::middleware('auth:api')->get('/visitorlog', function (Request $request) {
//     return $request->user();
// });

Route::group(
    [
        'namespace' => 'API'
    ],
    function () {
        // Visitor log devices
        Route::get('activate/device', 'VisitorLogDeviceController@activateDevice');
        // Route::get('me', 'AuthController@me');
        Route::post('login', 'AuthController@login');
        Route::post('app/health', 'ApplicationController@applicationHealth');

        //Only logged in users can access these routes
        Route::group(
            [
                'middleware' => ['auth:api']
            ],
            function () {
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

                // Visitor log devices
                Route::post('activate/device', 'VisitorLogDeviceController@activateDevice');
            }
        );
    }
);
