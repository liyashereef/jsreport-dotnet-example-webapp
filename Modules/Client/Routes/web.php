<?php

Route::group(['middleware' => ['web', 'auth', 'permission:view_client'], 'prefix' => 'client',], function () {
    //Route::get('/', 'ClientController@index');
    Route::get('/employee-feedback', array('as' => 'client.employee-rating', 'uses' => 'ClientEmployeeFeedbackController@index'));
    Route::group(['middleware' => ['permission:review_client_feedback']], function () {
        Route::get('/employee-feedback/{id}', array('as' => 'client.employee-rating.edit', 'uses' => 'ClientEmployeeFeedbackController@edit'));
    });
    Route::post('/employee-feedback/store', array('as' => 'client.employee-rating.store', 'uses' => 'ClientEmployeeFeedbackController@store'));
    Route::get('/get-employee/{prj_id}', array('as' => 'client.employee-rating.get-employee', 'uses' => 'ClientEmployeeFeedbackController@getEmployeeList'));
    Route::get('/get-employee-rating-list', array('as' => 'client.employee-rating.get-employee-rating-list', 'uses' => 'ClientEmployeeFeedbackController@getTableList'));

    Route::get('/client-concern', array('as' => 'client.concern', 'uses' => 'ClientConcernController@index'));
    Route::get('/client-concern-list', array('as' => 'client.concern-list', 'uses' => 'ClientConcernController@getTableList'));
    Route::post('/client-concern/store', array('as' => 'client-concern.store', 'uses' => 'ClientConcernController@store'));
    Route::group(['middleware' => ['permission:review_client_concern']], function () {
        Route::get('/client-concern/{id}', array('as' => 'client-concern.edit', 'uses' => 'ClientConcernController@edit'));
    });
    Route::get('/visitor', array('as' => 'client-visitor', 'uses' => 'VisitorController@index'));
    Route::get('/client-visitor-list', array('as' => 'client-visitor.list', 'uses' => 'VisitorController@getList'));
    Route::post('/client-visitor', array('as' => 'client-visitor.store', 'uses' => 'VisitorController@store'));
    Route::get('/client-visitor/remove/{id}', array('as' => 'client-visitor.destroy', 'uses' => 'VisitorController@destroy'));
    Route::get('/client-visitor/single/{id}', array('as' => 'client-visitor.single', 'uses' => 'VisitorController@getById'));

    Route::group(['middleware' => ['permission:view_all_customers_in_visitor_screening|view_allocated_customers_in_visitor_screening']], function () {
        Route::get('/visitor/screenings', array('as' => 'client-visitor.screening-submission', 'uses' => 'VisitorLogScreeningSubmissionController@index'));
        Route::get('/visitor/screenings/list', array('as' => 'client-visitor.screening-submission.list', 'uses' => 'VisitorLogScreeningSubmissionController@getList'));
        Route::get('/visitor/screenings/attempted-question-answers/{id}', array('as' => 'client-visitor.screening-submission.attemptedQuestionAndAnswers', 'uses' => 'VisitorLogScreeningSubmissionController@getAttemptedQuestionAndAnswers'));
    });

    Route::group(['middleware' => ['permission:view_all_visitorlog|view_allocated_visitorlog|create_visitorlog']], function () {
        Route::get('/visitor-log', array('as' => 'visitor-log.dashboard', 'uses' => 'VisitorLogController@index'));
        Route::post('/visitor-log/store', array('as' => 'visitor-log.store', 'uses' => 'VisitorLogController@store'));
        Route::group(['middleware' => ['permission:create_visitorlog']], function () {
            Route::post('/visitor-log/add', array('as' => 'visitor-log.add', 'uses' => 'VisitorLogController@addVisitorLog'));

        });
        Route::group(['middleware' => ['permission:exit_visitorlog|create_visitorlog']], function () {
            Route::get('/visitor-log/exit', array('as' => 'visitor-log.exit', 'uses' => 'VisitorLogController@exitSession'));

        });
        Route::get('/visitor-log/getList', array('as' => 'getCustomerTemplate.list', 'uses' => 'VisitorLogController@getTemplateList'));
        Route::get('/visitor-log/getCurrentLog', array('as' => 'getCurrentLog', 'uses' => 'VisitorLogController@getCurrentLogDetails'));
        Route::get('/visitor-log/getCheckoutLog', array('as' => 'getCheckoutLog', 'uses' => 'VisitorLogController@getCheckoutLogDetails'));
        Route::get('/visitor-log/getOvertimeLog', array('as' => 'getOvertimeLog', 'uses' => 'VisitorLogController@getOvertimeLogDetails'));
        Route::post('/visitor-log/checkout', array('as' => 'visitor-log.checkout', 'uses' => 'VisitorLogController@checkout')
        );
        Route::get('/visitor-log/load/{template_id?}', array('as' => 'visitor-log-form.load', 'uses' => 'VisitorLogController@loadVisitorLogForm'));
        Route::get('/visitor-log/details/{type?}/{customer?}', array('as' => 'visitor-log.details', 'uses' => 'VisitorLogDetailsController@index'));
        Route::get('/visitor-log/list/{type?}/{customer?}/{from?}/{to?}', array('as' => 'visitor-log.list', 'uses' => 'VisitorLogDetailsController@listAllVisitors'));
        Route::post('/visitor-log/uploadimage', array('as' => 'visitor-log.uploadimage', 'uses' => 'VisitorLogController@uploadImage'));

        Route::get('/visitor-log/view/{id?}', array('as' => 'visitor-log.view', 'uses' => 'VisitorLogController@viewLog'));

        Route::get('/visitor-log/checkintime', array('as' => 'visitor-log.checkintime', 'uses' => 'VisitorLogController@getCheckinTime'));
    });

});

Route::group([
    'middleware' => ['web', "auth"], 'as' => 'clientsurvey.',
    'prefix' => 'clientsurvey',
], function () {
    Route::get('/', ["as" => "index", "uses" => 'ClientSurveyController@index']);
    Route::post('/surveydata', ["as" => "surveydata", "uses" => 'ClientSurveyController@getSurveyData']);
    Route::post('/clientuserdata', ["as" => "clientuserdata", "uses" => 'ClientSurveyController@getClientuserdata']);
    Route::post('/saveclientuserdata', ["as" => "saveclientuserdata", "uses" => 'ClientSurveyController@setClientuserdata']);
    Route::post('/plotdata', ["as" => "plotdata", "uses" => 'ClientSurveyController@getClientchart']);
    Route::post('/plotdataanalytics', ["as" => "plotdataanalytics", "uses" => 'ClientSurveyController@getClientchartanalyticswidget']);
});
