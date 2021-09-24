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


Route::group(['prefix' => 'v1', 'namespace' => 'Modules\ClientApp\Http\Controllers'], function () {
    Route::post('/login', 'LoginController@login')->name('clientapp.login');
    Route::post('/forgot-password', 'LoginController@forgotPassword')->name('clientapp.forgotPassword');
    Route::get('/check-for-updates', 'ClientAppController@versionCheck')->name('clientapp.versionCheck');

    // Group with auth and customer id check
    Route::group(['middleware' => ['auth:api','customerVerifyClientApp']], function () {
        Route::get('/dashboard', 'DashboardController@dashboard')->name('clientapp.dashboard');
        Route::get('/incidents', 'ClientAppController@incident')->name('clientapp.incidents');
        Route::get('/visitor-log', 'VisitorLogController@visitorLogDetails')->name('clientapp.visitorLog');
        Route::get('/team-profile', 'ClientAppController@teamProfile')->name('clientapp.teamProfile');
        Route::get('/site-dashboard', 'SiteDashboardController@siteDashboard')->name('clientapp.siteDashboard');
        Route::get('/timesheets', 'TimeSheetController@timesheet')->name('clientapp.timesheets');
        Route::get('/client-concern', 'ClientController@clientConcern')->name('clientapp.clientConcern');
        Route::get('/client-feedback', 'ClientController@clientFeedback')->name('clientapp.clientFeedback');
        Route::get('/client/employees', 'ClientController@clientEmployeeList')->name('clientapp.clientEmployeeList');
        Route::post('/client-concern/store', 'ClientController@clientConcernStore')->name('clientapp.clientConcernStore');
        Route::post('/client-feedback/store', 'ClientController@clientFeedbackStore')->name('clientapp.clientFeedbackStore');
        Route::get('file/show/{id}/{module}/{attachment?}', '\App\Http\Controllers\AttachmentController@show')
            ->name('client.filedownload');
    });

    // Group with auth check only
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/me', 'LoginController@me')->name('clientapp.me');
        Route::get('/customers', 'ClientAppController@customer')->name('clientapp.customers');
        Route::get('/ratings', 'ClientAppController@ratings')->name('clientapp.ratings');
        Route::get('/client-feedback/types', 'ClientController@clientFeedbackTypes')->name('clientapp.clientFeedbackTypes');
        Route::get('/client-concern/severities', 'ClientController@clientSeverityTypes')->name('clientapp.clientSeverityTypes');
    });
});
