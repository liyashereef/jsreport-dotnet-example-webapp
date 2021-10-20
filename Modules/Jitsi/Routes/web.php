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


Route::group([
    'middleware' => ['web', 'auth'],
    'prefix' => 'meet',
    ], function () {
    Route::get('/', array(
        'as' => 'jitsi.index',
        'uses' => 'JitsiController@index'
    ));
    Route::get('blastmail', array(
        'as' => 'mailblast.index',
        'uses' => 'EmailController@viewMailDesigner'
    ));
    Route::post('saveblastmail', array(
        'as' => 'mailblast.saveblastmail',
        'uses' => 'EmailController@saveMailDesigner'
    ));

    Route::get('blastmail-reports', array(
        'as' => 'mailblast.reports',
        'uses' => 'EmailController@viewMailBlastReports'
    ));

    Route::get('blastmail-detailedview', array(
        'as' => 'mailblast.detailedview',
        'uses' => 'EmailController@viewMailDetailedView'
    ));


    Route::get('blastmail-reportslist', array(
        'as' => 'blastcomreport.list',
        'uses' => 'EmailController@viewMailBlastReportslist'
    ));

    Route::post(
        'initiateMeeting',
        array(
            'as' => 'jitsi.initiateMeeting',
            'uses' => 'JitsiController@initiateMeeting'
        )
    );
    Route::get('/bookings', array(
        'as' => 'jitsi.scheduledbooking',
        'uses' => 'JitsiController@getSchedules'
    ));
    Route::get('/schedulemeeting', array(
        'as' => 'jitsi.schedulemeeting',
        'uses' => 'JitsiController@scheduleMeetingview'
    ));
    Route::post(
        'savescheduleMeeting',
        array(
            'as' => 'jitsi.savescheduleMeeting',
            'uses' => 'JitsiController@saveScheduleMeeting'
        )
    );

    Route::post(
        'getEmployees',
        array(
            'as' => 'jitsi.getEmployees',
            'uses' => 'JitsiController@getEmployees'
        )
    );
    Route::post(
        'employeetomeeting',
        array(
            'as' => 'jitsi.employeetomeeting',
            'uses' => 'JitsiController@setEmployeetomeeting'
        )
    );
    Route::get(
        'joinmeeting/{sessid}',
        'JitsiController@joinMeeting'
    );

    Route::post(
        'jitsioperations',
        array(
            'as' => 'jitsi.operations',
            'uses' => 'JitsiController@setJitsioperations'
        )
    );

    Route::post(
        'jitsiremoveuser',
        array(
            'as' => 'jitsi.removeUser',
            'uses' => 'JitsiController@unsetUsers'
        )
    );

    Route::get(
        'getVideorecordings/{id}',
        array(
            'as' => 'jitsi.getVideorecordings',
            'uses' => 'JitsiController@getS3file'
        )
    );


    Route::post(
        'setMeetingstart',
        array(
            'as' => 'jitsi.setMeetingstart',
            'uses' => 'JitsiController@setJibriconferencestatus'
        )
    );
    Route::get(
        'joinscheduledmeeting/{id}',
        array(
            'as' => 'jitsi.joinScheduledmeeting',
            'uses' => 'JitsiController@joinScheduledmeeting'
        )
    );


    Route::post(
        'finishMeeting',
        array(
            'as' => 'jitsi.finishMeeting',
            'uses' => 'JitsiController@finishMeeting'
        )
    );
});

Route::group([
    'middleware' => ['web'],
    'prefix' => 'jitsiapp',
    ], function () {
    Route::get(
        'appmeetingroom/{roomname}/{username}/{owner}',
        'JitsiController@appJoinMeeting'
    );
});
