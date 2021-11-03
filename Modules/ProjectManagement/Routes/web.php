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
    'prefix' => 'projectmanagement',
], function () {
// project-report.rating.list

    Route::get('project/groupslist/{id}', array('as' => 'project.groupList', 'uses' => 'GroupController@getGroupsofProject'));

    Route::middleware(['permission:view_all_performance_reports|view_allocated_performance_reports'])->group(function () {
        Route::get('project/rating', 'ProjectManagementController@getPerformanceReport')->name('pm.get-project-rating');
        Route::get('project/rating/list/{startdate?}/{enddate?}/{project_id?}/{group_id?}/{emp_id?}', array('as' => 'project-report.rating.list', 'uses' => 'ProjectManagementController@getRatinglist'));
    });
    //General routes
    Route::middleware(['permission:view_all_reports|view_allocated_customer_reports|view_assigned_reports'])->group(function () {
        Route::get('report/{taskID?}', 'ProjectManagementController@index')->name('pm.report');
        Route::get('pm-project-report', 'ProjectManagementController@getProjectDetails')->name('pm.report-api');
    });

    Route::get('test/show-all-result/{task_id}', array('as' => 'user-task-status.show', 'uses' => 'TaskStatusController@getStatusofTasks'));


    Route::name('mail')->get('mail', 'ProjectManagementController@dueDateNotification');



    Route::middleware(['permission:create_task_all_customer|create_task_allocated_customer'])->group(function () {
        //Project routes
        Route::name('project')->get('project', 'ProjectController@index');
        Route::get('project/list', array('as' => 'project.list', 'uses' => 'ProjectController@list'));
        Route::post('project/store', array('as' => 'project.store', 'uses' => 'ProjectController@store'));
        Route::get('project/{id}', array('as' => 'project.show', 'uses' => 'ProjectController@show'));
        Route::get('project/{id}/users', ['as' => 'project.users', 'uses' => 'ProjectController@usersList']);


        //Group routes
        Route::get('group/list', array('as' => 'group.list', 'uses' => 'GroupController@list'));
        Route::post('group/store', array('as' => 'group.store', 'uses' => 'GroupController@store'));
        Route::get('group/{id}', array('as' => 'group.show', 'uses' => 'GroupController@show'));
        Route::get('project/{id}/groups', array('as' => 'project.groups', 'uses' => 'GroupController@getByProject'));

        //Task routes
        Route::post('task/store', array('as' => 'task.store', 'uses' => 'TaskController@store'));
        Route::get('task/{id}', array('as' => 'task.show', 'uses' => 'TaskController@show'));
        Route::get('project/{id}/tasks', array('as' => 'project.tasks', 'uses' => 'GroupController@projectTasks'));
        Route::get('group/{id}/tasks', array('as' => 'group.tasks', 'uses' => 'TaskController@groupTasks'));
        Route::get('task/progress/{id}/{is_completed}', array('as' => 'mark.progress', 'uses' => 'TaskController@markProgress'));
    });
    //Task status routes
    Route::get('tasks/{id}/status', array('as' => 'task.status', 'uses' => 'TaskStatusController@list'));
    Route::post('task-status/store', array('as' => 'task-status.store', 'uses' => 'TaskStatusController@store'));
    Route::get('task-status/{id}', array('as' => 'task-status.show', 'uses' => 'TaskStatusController@show'));
    Route::get('pm-emp-ratings', array('as' => 'emp.ratings', 'uses' => 'TaskController@ratings'));

    //Task Rating
    Route::post('task-rating/store', array('as' => 'task-rating.store', 'uses' => 'TaskController@storeRating'));

    Route::group(['middleware' => ['permission:lookup-remove-entries']], function () {
        Route::delete('project/{id}', array('as' => 'project.destroy', 'uses' => 'ProjectController@destroy'));
        Route::delete('group/{id}', array('as' => 'group.destroy', 'uses' => 'GroupController@destroy'));
        Route::delete('task/{id}', array('as' => 'task.destroy', 'uses' => 'TaskController@destroy'));
        Route::delete('task-status/{id}', array('as' => 'task.destroy', 'uses' => 'TaskController@destroy'));
    });
});


//Masters section
Route::group([
    'middleware' => ['web', 'auth', 'permission:view_admin'],
    'prefix' => 'admin',
    'namespace' => 'Admin'
], function () {
    //Implement admin section
    Route::name('interval')->get('interval', 'SettingsController@index');
    Route::post('interval/store', array('as' => 'interval.store', 'uses' => 'SettingsController@store'));
    Route::name('rating-tolerance')->get('rating-tolerance', 'RatingToleranceController@index');
    Route::post('rating-tolerance/store', array('as' => 'rating-tolerance.store', 'uses' => 'RatingToleranceController@store'));
});

