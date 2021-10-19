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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'learningandtraining'], function () {

    Route::get('dashboard', array('as' => 'learningandtraining.dashboard', 'uses' => 'Admin\DashboardController@index'));
    Route::get('dashboard/courses', array('as' => 'learningandtraining.dashboard.courses', 'uses' => 'Admin\DashboardController@getCourses'));
    Route::get('dashboard/courses-details', array('as' => 'learningandtraining.dashboard.courses', 'uses' => 'Admin\DashboardController@getCourses'));
    Route::get('dashboard/courses-details/{id}', array('as' => 'learningandtraining.dashboard.course-details', 'uses' => 'Admin\DashboardController@getCoursesDetails'));
    Route::get('dashboard/training-user--courses-details/{id}', array('as' => 'learningandtraining.dashboard.training-user-course-details', 'uses' => 'Admin\DashboardController@getTrainingCoursesDetails'));

    Route::get('dashboard/courses-details/users/{id}/{training_user?}', array('as' => 'learningandtraining.dashboard.course-details-users', 'uses' => 'Admin\DashboardController@getCoursesUserDetails'));
    Route::get('dashboard/reports', array('as' => 'learningandtraining.dashboard.reports', 'uses' => 'Admin\DashboardController@getReports'));
    Route::get('dashboard/reports_api', array('as' => 'learningandtraining.dashboard.reports_api', 'uses' => 'Admin\DashboardController@generateReports'));
    Route::get('dashboard/candidate-reports', array('as' => 'learningandtraining.dashboard.candidate-reports', 'uses' => 'Admin\DashboardController@viewCandidateReports'));
    Route::get('dashboard/candidate-reports_api', array('as' => 'learningandtraining.dashboard.candidate-reports_api', 'uses' => 'Admin\DashboardController@generateCandidateReports'));
    Route::post('dashboard/manual-completion', array('as' => 'learningandtraining.manual-completion', 'uses' => 'Admin\DashboardController@manualCompletion'));

    Route::get('course/training/', array('as' => 'course.training', 'uses' => 'CourseController@index'));
    Route::get('course/list/{type?}', array('as' => 'learningCourse.list', 'uses' => 'CourseController@courseList'));
    Route::get('course/training/single/{id}', array('as' => 'trainingCourse.single', 'uses' => 'CourseController@getSingle'));
    Route::post('course/register/store', array('as' => 'registerCourse.store', 'uses' => 'CourseController@registerCourse'));
    Route::name('course-admin')->get('course-admin', 'TrainingCourseController@index');
    Route::get('admin/course/list', array('as' => 'admin.course.list', 'uses' => 'TrainingCourseController@getList'));
    Route::get('admin/course/single/{id}', array('as' => 'admin.course.single', 'uses' => 'TrainingCourseController@getSingle'));
    Route::post('admin/course/store', array('as' => 'admin.course.store', 'uses' => 'TrainingCourseController@store'));
    Route::get('admin/course/destroy/{id}', array('as' => 'admin.course.destroy', 'uses' => 'TrainingCourseController@destroy'));

    /*** Training Course Category - start */
    Route::name('course-category-admin')->get('course-category-admin', 'TrainingCategoryController@index');
    Route::get('admin/course-category/list', array('as' => 'admin.course-category.list', 'uses' => 'TrainingCategoryController@getList'));
    Route::get('admin/course-category/single/{id}', array('as' => 'admin.course-category.single', 'uses' => 'TrainingCategoryController@getSingle'));
    Route::post('admin/course-category/store', array('as' => 'admin.course-category.store', 'uses' => 'TrainingCategoryController@store'));
    Route::get('admin/course-category/destroy/{id}', array('as' => 'admin.course-category.destroy', 'uses' => 'TrainingCategoryController@destroy'));
    /*** Training Course Category - end */

    /*** Training Course Content - start */
    Route::name('course-content-admin')->get('course-content-admin', 'CourseContentController@index');
    Route::get('course-content-admin/{id}', array('as' => 'learningandtraining.course-content-admin', 'uses' => 'CourseContentController@index'));
    Route::get('admin/course/content-list/{id}', array('as' => 'learningandtraining.course.content-lists', 'uses' => 'CourseContentController@getListByCourse'));
    Route::get('admin/course-content/list', array('as' => 'admin.course-content.list', 'uses' => 'CourseContentController@getList'));
    Route::get('admin/course-content/single/{id}', array('as' => 'admin.course-content.single', 'uses' => 'CourseContentController@getSingle'));
    Route::post('admin/course-content/store', array('as' => 'admin.course-content.store', 'uses' => 'CourseContentController@store'));
    Route::get('admin/course-content/destroy/{id}', array('as' => 'admin.course-content.destroy', 'uses' => 'CourseContentController@destroy'));
    Route::get('admin/course-content/get/{id}', array('as' => 'admin.course-content.get', 'uses' => 'CourseContentController@getContentDetails'));
    /*** Training Course Content - end */


    /*** Team management - start ------ **/
    Route::get('teams', array('as' => 'learningandtraining.team.list.page', 'uses' => 'Admin\TeamController@index'));
    Route::get('teams/all', array('as' => 'learningandtraining.team.list', 'uses' => 'Admin\TeamController@getTableList'));
    Route::get('teams/create', array('as' => 'learningandtraining.team.create', 'uses' => 'Admin\TeamController@create'));
    Route::get('teams/edit/{id}', array('as' => 'learningandtraining.team.edit-form', 'uses' => 'Admin\TeamController@create'));
    Route::post('teams/store', array('as' => 'learningandtraining.team.store', 'uses' => 'Admin\TeamController@store'));
    //  Route::post('teams/store', array('as' => 'learningandtraining.team.store', 'uses' => 'Admin\TeamController@store'));
    Route::get('teams/destroy/{id}', array('as' => 'learningandtraining.team.destroy', 'uses' => 'Admin\TeamController@destroy'));
    Route::get('admin/course-team-list/{course_id}', array('as' => 'learningandtraining.course.team-lists', 'uses' => 'Admin\TeamController@getListByCourse'));
    Route::get('admin/destory/team-course/{team_id}', array('as' => 'learningandtraining.course.team-course-unallocate', 'uses' => 'Admin\TeamController@removeAllAllocation'));
    /*** Team management - end ----- **/

    /*** Employee allocation to Team - start ------ **/
    Route::get('employee-allocation', array('as' => 'learningandtraining.team.employee-allocation.page', 'uses' => 'Admin\EmployeeAllocationController@index'));
    Route::post('employee-allocation', array('as' => 'learningandtraining.team.employee-allocation.store', 'uses' => 'Admin\EmployeeAllocationController@allocate'));
    Route::post('employee-unallocate', array('as' => 'learningandtraining.team.employee-allocation.remove', 'uses' => 'Admin\EmployeeAllocationController@unallocate'));
    Route::get('employee-allocation/list', array('as' => 'learningandtraining.team.employee-allocation.list', 'uses' => 'Admin\EmployeeAllocationController@getAllocationList'));

    /*** Employee allocation to Team - End ------ **/

    /*** Exam Settings - start ------ **/
    Route::get('exam-questions-settings/{id}', array('as' => 'learningandtraining.exam-questions-settings', 'uses' => 'Admin\TrainingTestSettingsController@index'));
    Route::get('exam-questions-settings/list/{id}', array('as' => 'learningandtraining.settings-list', 'uses' => 'Admin\TrainingTestSettingsController@getList'));
    Route::post('exam-questions-settings/store', array('as' => 'learningandtraining.exam-questions-settings.store', 'uses' => 'Admin\TrainingTestSettingsController@storeSettings'));
    Route::get('exam-questions-settings/edit/{id}', array('as' => 'learningandtraining.exam-questions-settings.single', 'uses' => 'Admin\TrainingTestSettingsController@getSingleSetting'));
    Route::get('exam-questions-settings/destroy/{id}', array('as' => 'learningandtraining.exam-questions-settings.destroy', 'uses' => 'Admin\TrainingTestSettingsController@deleteSettings'));
    /*** Exam Settings - End ------ **/

    /*** Exam Questions - Start ------ **/
    Route::get('exam-questions/{id}', array('as' => 'learningandtraining.exam-questions', 'uses' => 'Admin\TrainingTestQuestionController@index'));
    Route::get('exam-questions/list/{id}', array('as' => 'learningandtraining.questions-list', 'uses' => 'Admin\TrainingTestQuestionController@getListQuestions'));
    Route::post('exam-questions/store', array('as' => 'learningandtraining.exam-questions.store', 'uses' => 'Admin\TrainingTestQuestionController@store'));
    Route::get('exam-questions/edit/{id}', array('as' => 'learningandtraining.exam-questions.single', 'uses' => 'Admin\TrainingTestQuestionController@getSingleQuestion'));
    Route::get('exam-questions/destroy/{id}', array('as' => 'learningandtraining.exam-questions.destroy', 'uses' => 'Admin\TrainingTestQuestionController@deleteQuestion'));
    /*** Exam Questions - End ------ **/
});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'learning',], function () {

    Route::get('dashboard', array('as' => 'learning.dashboard', 'uses' => 'Learner\DashboardController@index'));
    Route::get('dashboard/course-list/{id}', array('as' => 'learning.dashboard.course-list', 'uses' => 'Learner\DashboardController@getCourseList'));
    Route::get('dashboard/completed-course-list', array('as' => 'learning.dashboard.completed-course-list', 'uses' => 'Learner\DashboardController@getCompletedCourses'));
    //    Route::post('dashboard/search/course-list', array('as' => 'learning.dashboard.search-course-list', 'uses' => 'Learner\DashboardController@searchCourseList'));
    Route::get('course-learner', array('as' => 'course-learner', 'uses' => 'Learner\LearningCourseController@index'));
    Route::get(
        'course-learner/view/{id}',
        array('as' => 'course-learner.view',
            'uses' => 'Learner\LearningCourseController@view')
    )
        ->middleware(['Modules\LearningAndTraining\Http\Middleware\UserCanAccessCourse']);
    Route::get('course-content/view-video/{id}', array('as' => 'course-content.video.view', 'uses' => 'Learner\LearningCourseController@videoView'));
    Route::get('course-content/view-image/{id}', array('as' => 'course-content.image.view', 'uses' => 'Learner\LearningCourseController@imageView'));
    Route::get('course-content/view-pdf/{id}', array('as' => 'course-content.pdf.view', 'uses' => 'Learner\LearningCourseController@pdfView'));
    Route::post('content-update', array('as' => 'content-update', 'uses' => 'Learner\LearningCourseController@contentUpdate'));
    Route::post('course-rating', array('as' => 'course-rating', 'uses' => 'Learner\LearningCourseController@courseRating'));

    Route::get('test/questions/show/{id}', array('as' => 'test.show-questions', 'uses' => 'TrainingTestController@index'));
    Route::post('test/questions/store', array('as' => 'test-results.store', 'uses' => 'TrainingTestController@store'));
    Route::get('test/result-show/{id}', array('as' => 'test-result.show', 'uses' => 'TrainingTestController@getResultDetailById'));
    Route::get('test/show-all-result/{user_id}/{course_id}', array('as' => 'user-test-results.show', 'uses' => 'TrainingTestController@showAllResults'));
});
