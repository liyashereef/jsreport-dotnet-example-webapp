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


Route::group(['namespace' => 'Modules\Recruitment\Http\Controllers'], function () {
    Route::group(['middleware' => ['auth:rec-candidate-api','scopes:rec_candidate']], function () {
        Route::post('getProcessTab', 'API\RecApiController@getProcessTab');

        /* Candidate Tabs - Start */
        Route::get('candidate/questions', 'API\RecApiController@getScreeningQuestion');
        Route::get('candidate/personality', 'API\RecApiController@getPersonalityQuestions');
        Route::get('candidate/competency', 'API\RecApiController@getCompetencyMatrix');
        Route::get('candidate/attachments', 'API\RecApiController@getAttachments');
        Route::get('candidate/getProfile', 'API\RecApiController@getProfile');
        Route::get('candidate/uniform', 'API\RecApiController@getUniform');
        Route::get('candidate/training/{courseType}', 'API\RecApiController@getTraining');
        Route::get('candidate/course-learner/view/{id}', 'API\RecApiController@viewCourse');
        Route::get('course-content/view-video/{id}', 'API\RecApiController@videoView');
        Route::get('course-content/view-image/{id}', 'API\RecApiController@imageView');
        Route::get('course-content/view-pdf/{id}', 'API\RecApiController@pdfView');
        Route::post('candidate/test/questions/store', 'API\RecApiController@storeTestResults');
        Route::get('candidate/test/questions/show/{id}', 'API\RecApiController@getTest');
        Route::post('candidate/test/rating/store', 'API\RecApiController@storeUserRating');
        Route::post('candidate/content-update', 'API\RecApiController@contentUpdate');
        /* Candidate Tabs - End */
        Route::get('candidate/test/show-all-result/{course_id}', 'API\RecApiController@showAllResults');
        Route::get('candidate/test/result-show/{id}', 'API\RecApiController@getResultDetailById');
        Route::get('candidate/documents/{processTabId}', 'API\RecApiController@getDocuments');
        Route::get('candidate/update-tracking/{tabid}', 'API\RecApiController@updateTrackingOnDocumentUpdate');


        /* Candidate Credentials - Start */
        Route::get('candidate-credentials/single', 'API\RecApiController@getSingleCandidateCredential');
        Route::post('candidate-credentials/store', 'API\RecApiController@updateCandidateCredential');
        //  Route::post('login', 'API\RecApiController@login');
        Route::get('candidate/profile', 'API\RecApiController@candidateProfile');
        /* Candidate Credentials - End */
        Route::post('candidate/agree-terms', 'API\RecApiController@acceptTermsAndConditions');
        Route::post('candidate/store-screening-questions', 'API\RecApiController@storeScreeningQuestions');
        Route::post('candidate/storePersonality', 'API\RecApiController@storePersonality');
        Route::post('candidate/storeProfile', 'API\RecApiController@storeProfile');
        Route::post('candidate/store-competency', 'API\RecApiController@storeCompetency');
        Route::post('candidate/store-profile-pic', 'API\RecApiController@storeProfilePic');
        Route::post('candidate/update-form-complete', 'API\RecApiController@updateFormComplete');

        Route::get('candidate/getS3SignedUrl/{fileName}/{prefix}', 'API\RecApiController@getS3SignedUrl');

        Route::get('candidate/geolocation/{address}', 'API\RecApiController@postalCodeResolve');


        /* Job - start */
        Route::get('job/list', 'API\RecApiController@getJobList');
        Route::post('job/update-jobpreference', 'API\RecApiController@updateJobPreference');
        Route::get('job/appliedJob', 'API\RecApiController@getJobAppliedList');
        /* Job - end */

        // Rec Candidate Document Store
        Route::post('candidate/document-update', 'API\RecApiController@storeCandidateDocument');
        Route::get('candidate/document-remove/{id}', 'API\RecApiController@destroyCandidateDocument');

        Route::post('candidate/attachment-update', 'API\RecApiController@storeCandidateAttachment');
        Route::get('candidate/attachment-remove/{id}', 'API\RecApiController@destroyCandidateAttachment');



        Route::post('candidate-store-uniform-measurement', 'API\RecApiController@storeUniformMeasurement');
        // Route::post('forgot', 'Auth\ForgotPasswordController');
        // Route::post('reset', 'Auth\ResetPasswordController@reset');
    });

    /*   Route::group([
      'namespace' => 'Auth',
      'middleware' => 'api',
       ], function () {

          Route::post('loginacess', 'LoginController@login');
           Route::post('login', 'LoginController@login');
           Route::post('forgot', 'ForgotPasswordController');
           Route::post('reset', 'ResetPasswordController@reset');

       }); */
});

Route::group(['namespace' => 'Modules\Recruitment\Http\Controllers\Auth'], function () {

    Route::post('login', 'LoginController@login');
    Route::post('reset-password', 'LoginController@resetPassword');
    Route::post('forgot-password', 'LoginController@forgotPassword');
    Route::group(['middleware' => ['auth:rec-candidate-api','scopes:rec_candidate']], function () {
        Route::post('logout', 'LoginController@logout');
    });
});
