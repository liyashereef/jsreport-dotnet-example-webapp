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
        'namespace' => 'API',
        'prefix' =>'v1'
    ],
    function () {
        // Visitor log devices
        Route::get('activate/device', 'VisitorLogDeviceController@activateDevice');
        Route::post('login', 'AuthController@login');
        // Route::post('app/health', 'ApplicationController@applicationHealth');

        //Only logged in users can access these routes
        Route::group(
            [
                'middleware' => ['auth:api']
            ],
            function () {
                Route::get('me', 'AuthController@me');
                Route::post('logout', 'AuthController@logout');

                //Routes for getting Customers vistor log template
                // Route::get('customers/{customer_id}/templates', 'VisitorLogApiController@getCustomerTemplates');
                // Route::get('customers/{customer_id}/templates/{template_id}', 'VisitorLogApiController@getTemplate');
                // Route::get('customers/visitor-types', 'VisitorLogApiController@getVisitorTypes');
                Route::get('visitors', 'VisitorLogApiController@fetchVisitors');
                // Route::post('visitors/store', 'VisitorLogApiController@storeVisitors');
                // Route::get('customers/{customer_id}/visitors', 'VisitorLogApiController@fetchVisitorsFallback'); //TODO:remove later
                // Route::get('customers-allocated', 'VisitorLogApiController@getCustomers');
                Route::get('logs', 'VisitorLogApiController@getPeerSyncVisitorLogs');
                Route::post('logs/store', 'VisitorLogApiController@storeVisitorLogs');
                Route::get('tac', 'VisitorLogApiController@getTermsAndCondition');

                // Visitor screening questions
                // Route::get('customers/{customer_id}/screening-questions', 'VisitorLogApiController@fetchScreeningQuestions');
                Route::post('screening/store', 'VisitorLogApiController@storeScreeningQuestion');

                // Visitor log devices
                Route::post('devices/activate', 'VisitorLogDeviceController@activateDevice');
            }
        );
    }
);
