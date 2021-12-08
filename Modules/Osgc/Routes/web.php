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

Route::group([  'middleware' => ['web', 'auth', 'permission:view_admin'], 'prefix' => 'admin'], function()
{
    /* OSGC Course - start */
    Route::name('osgc-course')->get('osgc-course', 'Admin\OsgcCourseLookupController@index');
    Route::get('osgc-course/list', array('as' => 'osgc-course.list', 'uses' => 'Admin\OsgcCourseLookupController@getList'));
    Route::post('osgc-course/store', array('as' => 'osgc-course.store', 'uses' => 'Admin\OsgcCourseLookupController@store'));
    Route::get('osgc-course/single/{id}', array('as' => 'osgc-course.single', 'uses' => 'Admin\OsgcCourseLookupController@getSingle'));
    Route::get('osgc-course/destroy/{id}', array('as' => 'osgc-course.destroy', 'uses' => 'Admin\OsgcCourseLookupController@destroy'));
    Route::get('osgc-course/deleteCourseHeader/{id}', array('as' => 'osgc-course.deleteCourseHeader', 'uses' => 'Admin\OsgcCourseLookupController@deleteCourseHeader'));
    Route::get('osgc-course-content/{id}', array('as' => 'osgc-course-contents', 'uses' => 'Admin\OsgcCourseLookupController@courseContent'));
    Route::get('osgc-course-contents/list', array('as' => 'osgc-course-contents.list', 'uses' => 'Admin\OsgcCourseLookupController@getCourseContentList'));
    Route::post('osgc-course-contents/store', array('as' => 'osgc-course-contents.store', 'uses' => 'Admin\OsgcCourseLookupController@saveCourseContents'));
    Route::get('osgc-course-contents/single/{id}', array('as' => 'osgc-course-contents.single', 'uses' => 'Admin\OsgcCourseLookupController@getSingleCourseContent'));
    Route::get('osgc-course-contents/sectionList', array('as' => 'osgc-course-contents.sectionList', 'uses' => 'Admin\OsgcCourseLookupController@getsectionList'));
    Route::post('osgc-course-contents/storeStudyGuide', array('as' => 'osgc-course-contents.storeStudyGuide', 'uses' => 'Admin\OsgcCourseLookupController@saveStudyGuide'));
    Route::post('osgc-course-contents/checkStudyGuideExist', array('as' => 'osgc-course-contents.checkStudyGuideExist', 'uses' => 'Admin\OsgcCourseLookupController@getStudyGuide'));
    Route::get('osgc-course/activateCourse/{id}', array('as' => 'osgc-course.activateCourse', 'uses' => 'Admin\OsgcCourseLookupController@activateCourse'));
    Route::get('osgc-course/deactivateCourse/{id}', array('as' => 'osgc-course.deactivateCourse', 'uses' => 'Admin\OsgcCourseLookupController@deactivateCourse'));
    Route::get('osgc-course/fetchS3Details', array('as' => 'osgc-course.fetchS3Details', 'uses' => 'Admin\OsgcCourseLookupController@getS3Details'));
    /* OSGC Course - start */

    /*** Exam Settings - start ------ **/
    Route::get('osgc-questions-settings/{id}', array('as' => 'osgc.exam-questions-settings', 'uses' => 'Admin\OsgcTestSettingsController@index'));
    Route::get('osgc-questions-settings/list/{id}', array('as' => 'osgc.settings-list', 'uses' => 'Admin\OsgcTestSettingsController@getList'));
    Route::post('osgc-questions-settings/store', array('as' => 'osgc.exam-questions-settings.store', 'uses' => 'Admin\OsgcTestSettingsController@storeSettings'));
    Route::get('osgc-questions-settings/edit/{id}', array('as' => 'osgc.exam-questions-settings.single', 'uses' => 'Admin\OsgcTestSettingsController@getSingleSetting'));
    Route::get('osgc-questions-settings/destroy/{id}', array('as' => 'osgc.exam-questions-settings.destroy', 'uses' => 'Admin\OsgcTestSettingsController@deleteSettings'));
    /*** Exam Settings - End ------ **/

    /*** Exam Questions - Start ------ **/
    Route::get('osgc-questions/{id}', array('as' => 'osgc.exam-questions', 'uses' => 'Admin\OsgcTestQuestionController@index'));
    Route::get('osgc-questions/list/{id}', array('as' => 'osgc.questions-list', 'uses' => 'Admin\OsgcTestQuestionController@getListQuestions'));
    Route::post('osgc-questions/store', array('as' => 'osgc.exam-questions.store', 'uses' => 'Admin\OsgcTestQuestionController@store'));
    Route::get('osgc-questions/edit/{id}', array('as' => 'osgc.exam-questions.single', 'uses' => 'Admin\OsgcTestQuestionController@getSingleQuestion'));
    Route::get('osgc-questions/destroy/{id}', array('as' => 'osgc.exam-questions.destroy', 'uses' => 'Admin\OsgcTestQuestionController@deleteQuestion'));
    /*** Exam Questions - End ------ **/

    /*** osgc users - Start ------ **/
    Route::name('osgc-users')->get('osgc-users', 'Admin\OsgcCourseLookupController@userIndex');
    Route::get('osgc-users/list', array('as' => 'osgc-users.list', 'uses' => 'Admin\OsgcCourseLookupController@getUserList'));
    Route::get('activateUsers/{id}', array('as' => 'osgc-users.activateUsers', 'uses' => 'Admin\OsgcCourseLookupController@activateUser'));
    Route::get('deactivateUsers/{id}', array('as' => 'osgc-users.deactivateUsers', 'uses' => 'Admin\OsgcCourseLookupController@deactivateUser'));
    Route::get('reset-password/{email}', array('as' => 'osgc-users.reset-password', 'uses' => 'Admin\OsgcCourseLookupController@resetPassword'));
    Route::get('user-export', array('as' => 'osgc-users.user-export', 'uses' => 'Admin\OsgcCourseLookupController@registeredUserExport'));
    Route::get('osgc-user/edit/{id}', array('as' => 'osgc-user.single', 'uses' => 'Admin\OsgcCourseLookupController@getSingleUser'));
    Route::post('osgc-user/store', array('as' => 'osgc-user.store', 'uses' => 'Admin\OsgcCourseLookupController@storeUser'));
    /*** osgc users - End ------ **/

});
Route::group(['middleware' => ['web', 'auth', 'permission:view_osgc_registered_users'],'prefix' => 'osgc', 'namespace' => 'Osgc'], function()
{
    Route::get('registered-users', array('as' => 'osgc.registered-users', 'uses' => 'OsgcController@index'));
    Route::get('registered-users/list/{course_completion_status?}', array('as' => 'registered-users.list', 'uses' => 'OsgcController@getList'));
});

Route::prefix('osgc')
    ->name('osgc.')
    ->middleware(['web'])
    ->group(function()
    {

        Route::get('/registration', 'OsgcUserController@registration')->name('registration')->middleware('guest:osgcuser');
        Route::get('/', 'OsgcUserController@login')->name('login')->middleware('guest:osgcuser');
        Route::get('/login', 'OsgcUserController@login')->name('login');
        Route::post('/add-user', 'OsgcUserController@store');
        Route::get('/activate-account/{token}', 'OsgcUserController@activateAccount');
        Route::group(['middleware' => ['login-validation-log']], function () {
            Route::post('/check-login-user', 'OsgcUserController@checkLoginUser')->name('check-login-user');
        });
        Route::get('/forgot-password', 'OsgcUserController@forgotPassword')->middleware('guest:osgcuser');
        Route::post('/reset-password', 'OsgcUserController@resetPassword');




        Route::group(['middleware' => ['web','auth:osgcuser']],function (){
            Route::post('/logout',  array('as' => 'logout', 'uses' => 'OsgcUserController@logout'));
            Route::get('/home', array('as' => 'home', 'uses' => 'OsgcController@index'));
            Route::get('/guard-training', array('as' => 'guardTraining', 'uses' => 'OsgcController@guardTraining'));
            Route::get('/paymentSuccess',  array('as' => 'paymentSuccess', 'uses' => 'OsgcController@coursePaymentSuccess'));
            Route::post('/payCourse',  array('as' => 'payCourse', 'uses' => 'OsgcController@stripePost'));
            Route::get('/change-password', 'OsgcUserController@changePassword');
            Route::post('/update-password', 'OsgcUserController@updatePassword');
            Route::post('/showCourse', array('as' => 'showCourse', 'uses' => 'OsgcController@showCourse'));

            Route::group(['middleware' => ['read-osgc-course']],function (){
                Route::get('/course/{course_id}',  array('as' => 'course', 'uses' => 'OsgcCourseController@courseDetails'));
                Route::post('/showCourseContent', array('as' => 'showCourseContent', 'uses' => 'OsgcCourseController@showCourseContent'));
                Route::post('/showTestContent', array('as' => 'showTestContent', 'uses' => 'OsgcCourseController@showTestContent'));
                Route::post('/storeOsgcTest', array('as' => 'storeOsgcTest', 'uses' => 'OsgcCourseController@storeTest'));
                Route::post('/checkCourseHavingTest', array('as' => 'checkCourseHavingTest', 'uses' => 'OsgcCourseController@checkCourseHavingTest'));
                Route::post('/getCertificate',  array('as' => 'getCertificate', 'uses' => 'OsgcCourseController@getCourseCertificate'));
                Route::post('/downloadStudyGuide', array('as' => 'downloadStudyGuide', 'uses' => 'OsgcCourseController@downloadStudyGuide'));
                Route::post('downloadCertificate', array('as' => 'downloadCertificate', 'uses' => 'OsgcCourseController@downloadCertificate'));
                Route::post('/checkContentActive', array('as' => 'checkContentActive', 'uses' => 'OsgcCourseController@checkContentActive'));
                Route::post('/showTestResult', array('as' => 'showTestResult', 'uses' => 'OsgcCourseController@showTestResult'));
                Route::post('/checkLastCourse', array('as' => 'checkLastCourse', 'uses' => 'OsgcCourseController@checkLastCourse'));
                Route::post('/checkStudyGuideExist', array('as' => 'checkStudyGuideExist', 'uses' => 'OsgcCourseController@getStudyGuide'));
            });

        });;
    });
