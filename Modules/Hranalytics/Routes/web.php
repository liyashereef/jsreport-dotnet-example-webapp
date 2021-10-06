<?php

Route::group(['middleware' => ['web', 'prevent-back-history']], function () {
    Route::get('apply/{id}/view', array('as' => 'applyjob.view', 'uses' => 'JobApplicationController@viewApplication'));
    Route::get('apply', array('as' => 'applyjob', 'uses' => 'JobApplicationController@applyjob'));
});

Route::group(['middleware' => ['web'],], function () {
    /* Apply job - candidate - start */

    Route::post('apply/login', array('as' => 'applyjob.login', 'uses' => 'JobApplicationController@login'));
    Route::get('apply/logout', array('as' => 'applyjob.logout', 'uses' => 'JobApplicationController@logout'));
    Route::get('apply/dashboard', array('as' => 'applyjob.dashboard', 'uses' => 'JobApplicationController@dashboard'));
    Route::get('apply/jobList', array('as' => 'applyjob.jobList', 'uses' => 'JobApplicationController@getJobList'));
    Route::post('apply/store', array('as' => 'applyjob.store', 'uses' => 'JobApplicationController@store'));
    Route::post('apply/store/screening', array('as' => 'applyjob.storescreeningquestion', 'uses' => 'JobApplicationController@store_screening_questions'));
    Route::post('apply/store/uniform', array('as' => 'applyjob.storeUniform', 'uses' => 'JobApplicationController@store_uniform_measures'));
    Route::post('apply/attachment', array('as' => 'applyjob.attachment', 'uses' => 'JobApplicationController@storeAttachment'));

    Route::get('apply/previous', array('as' => 'applyjob.previous', 'uses' => 'JobApplicationController@previousapplication'));
    Route::get('apply/pdf/{id}', array('as' => 'applyjob.pdf', 'uses' => 'JobApplicationController@downloadPDF'));
    Route::post('apply/store/personality', array('as' => 'applyjob.storepersonality', 'uses' => 'JobApplicationController@store_personality'));
    Route::post('apply/store/competency-matrix', array('as' => 'applyjob.competencymatrix', 'uses' => 'JobApplicationController@store_competency_matrix'));
    Route::get('get-address-form', array('as' => 'previousaddress.add', function () {
        return view('hranalytics::job-application.partials.profile.previous-address');
    }));
    Route::get('get-positon-form', array('as' => 'position.add', function () {
        return view('hranalytics::job-application.partials.profile.employement-history');
    }));
    Route::get('get-reference-form', array('as' => 'reference.add', function () {
        return view('hranalytics::job-application.partials.profile.references');
    }));
    Route::get('get-education-form', array('as' => 'education.add', function () {
        return view('hranalytics::job-application.partials.profile.education');
    }));
    Route::get('get-language-form', array('as' => 'language.add', 'uses' => 'JobApplicationController@getOtherlanguages'));

    /* Apply job - candidate - end */
});

Route::group(['middleware' => ['web', 'auth', 'permission:view_hranalytics'], 'prefix' => 'hranalytics'], function () {
    Route::get('/', 'HranalyticsController@index');
    Route::middleware(['permission:create-job|edit-job|delete-job|archive-job|job-approval|hr-tracking|job-attachement-settings|list-jobs-from-all|candidate-mapping|view_all_candidates_candidate_geomapping'])->group(function () {
        Route::name('job')->get('job', 'JobController@index');
        Route::get('job/list/{status?}', array('as' => 'job.list', 'uses' => 'JobController@getList'));
        Route::get('job/mapping', array('as' => 'job.mapping', 'uses' => 'JobController@jobMapping'));
    });
    Route::middleware(['permission:create-job'])->group(function () {
        Route::get('job/create', array('as' => 'job.create', 'uses' => 'JobController@create'));
    });
    Route::middleware(['permission:edit-job'])->group(function () {
        Route::get('job/edit/{id}', array('as' => 'job.edit', 'uses' => 'JobController@edit'));
    });
    Route::middleware(['permission:edit-job|create-job'])->group(function () {
        Route::post('job/store', array('as' => 'job.store', 'uses' => 'JobController@store'));
    });
    Route::middleware(['permission:job-approval'])->group(function () {
        Route::post('job/update-status/{job_id}', array('as' => 'job.update-status', 'uses' => 'JobController@changeStatus'));
    });
    Route::get('customer/{id}/details', array('as' => 'project.details', 'uses' => 'JobController@getCustomer'));
    Route::get('job/view/{id}', array('as' => 'job.view', 'uses' => 'JobController@viewJob'));

    Route::middleware(['permission:job-attachement-settings'])->group(function () {
        Route::post('job/attachment-settings/{job_id}', array('as' => 'job.attachment-settings', 'uses' => 'JobController@setMandatoryAttachements'));
    });
    Route::get('job/view-description/{job_id}', array('as' => 'job.view-description', 'uses' => 'JobController@viewJobDescription'));

    Route::middleware(['permission:archive-job'])->group(function () {
        Route::post('job/archive', array('as' => 'job.archive', 'uses' => 'JobController@archiveJob'));
    });

    Route::middleware(['permission:hr-tracking|hr-tracking-detailed-view|track_all_candidates|view_all_candidates_candidate_onboardingstatus'])->group(function () {
        Route::get('job/hr-tracking/{job_id}', array('as' => 'job.hr-tracking', 'uses' => 'JobController@hrTracking'));
        Route::get('candidate/{candidate_id}/{job_id}/track', array('as' => 'candidate.track', 'uses' => 'CandidateController@trackCandidate'));
    });

    Route::middleware(['permission:hr-tracking|track_all_candidates'])->group(function () {
        Route::post('job/hr-tracking/{job_id}', array('as' => 'job.hr-tracking', 'uses' => 'JobController@hrTrackingStore'));
        Route::post('candidate/{candidate_id}/{job_id}/track', array('as' => 'candidate.track-store', 'uses' => 'CandidateController@trackCandidateStore'));
    });

    Route::middleware(['permission:delete-hr-tracking'])->group(function () {
        Route::get('job/remove-hr-tracking-step/{job_id}/{step_id}', array('as' => 'job.remove-hr-tracking-step', 'uses' => 'JobController@hrTrackingRemove'));
        Route::get('candidate/remove-hr-tracking-step/{job_id}/{candidate_id}/{step_id}', array('as' => 'candidate.remove-hr-tracking-step', 'uses' => 'CandidateController@hrTrackingRemove'));
    });

    Route::middleware(['permission:job-tracking-summary'])->group(function () {
        Route::get('job/hr-tracking-summary', array('as' => 'job.hr-tracking-summary', 'uses' => 'JobController@hrTrackingSummary'));
        Route::get('job/hr-tracking-summary-list', array('as' => 'job.hr-tracking-summary.list', 'uses' => 'JobController@hrTrackingSummaryList'));
    });

    Route::middleware(['permission:candidate-screening-summary'])->group(function () {
        Route::name('candidate')->get('candidate', 'CandidateController@index');
        Route::get('candidate/screening-summary-list', array('as' => 'candidate.screening-summary-list', 'uses' => 'CandidateController@screeningSummaryList'));
        Route::get('candidate/candidate-export', array('as' => 'candidate.candidate-export', 'uses' => 'CandidateController@candidateExport'));
        Route::get('candidate/candidateGeomapping-export', array('as' => 'candidate.candidateGeomapping-export', 'uses' => 'CandidateController@candidateGeomappingExport'));
        Route::post('candidate/candidateGeomapping-store-candidate-sessionalData', array('as' => 'candidate.candidateGeomapping-storeCandidateSessionalData', 'uses' => 'CandidateController@storeCandidateSessionalData'));
    });
    Route::get('candidate/test-score-attachment/download/{file}', array('as' => 'test-score-document.download', 'uses' => 'CandidateController@downloadTestScoreDocument'));
    Route::get('candidate/force-attachemnt/download/{file}', array('as' => 'force-document.download', 'uses' => 'CandidateController@downloadForceDocument'));

    Route::middleware(['permission:edit-candidate'])->group(function () {
        Route::get('candidate/{candidate_id}/{job_id}/edit', array('as' => 'candidate.edit', 'uses' => 'CandidateController@editCandidateJob'));
    });

    Route::middleware(['permission:candidate-mapping'])->group(function () {
        Route::get('candidate/mapping', array('as' => 'candidate.mapping', 'uses' => 'CandidateController@mapping'));
        Route::get('candidate/{job_id}/plot-in-map-with-customer', array('as' => 'candidate.plot-in-map-with-customer', 'uses' => 'CandidateController@plotJobCandidatesMap'));
        Route::post('candidate/schdeule/mapping', array('as' => 'candidate.schedule.mapping', 'uses' => 'CandidateScheduleController@plotJobschdeuleEmployeeMap'));
    });

    Route::get('candidate/{candidate_id}/{job_id}/view', array('as' => 'candidate.view', 'uses' => 'CandidateController@viewCandidate'));

    Route::get('candidate/remove-attachment/{candidate_id}/{attachment_id}', array('as' => 'candidate.remove-attachment', 'uses' => 'CandidateController@removeCandidateAttachment'));
    Route::middleware(['permission:candidate-approval'])->group(function () {
        Route::post('candidate/primary-screening', array('as' => 'candidate.primary-screening', 'uses' => 'CandidateController@updateCandidateJobStatus'));
    });

    //Route::middleware(['permission:candidate-rate-screening-question-answers|candidate-add-interview-notes|view_interview_notes'])->group(function () {
    Route::get('candidate/{candidate_id}/{job_id}/review', array('as' => 'candidate.review', 'uses' => 'CandidateController@reviewCandidate'));
    //});
    Route::middleware(['permission:candidate-rate-screening-question-answers'])->group(function () {
        Route::post('candidate/review/answers', array('as' => 'candidate.review.answers', 'uses' => 'CandidateController@reviewScreeningAnswers'));
    });
    Route::middleware(['permission:candidate-add-interview-notes'])->group(function () {
        Route::post('candidate/interview', array('as' => 'candidate.interview', 'uses' => 'CandidateController@addInterviewNotes'));
    });

    Route::get('candidate/get-job/{job_id}', array('as' => 'candidate.get-job', 'uses' => 'CandidateController@getJob'));
    Route::middleware(['permission:candidate-tracking-summary'])->group(function () {
        Route::get('candidate/summary', array('as' => 'candidate.summary', 'uses' => 'CandidateController@candidateSummary'));
        Route::get('candidate/summarylist', array('as' => 'summary.list', 'uses' => 'CandidateController@candidateSummaryList'));
    });

    /* Dashboard - start */
    Route::name('dashboard')->get('dashboard', 'DashboardController@index');
    Route::get('dashboard/drilldown/{type}', array('as' => 'dashboard.drilldown', 'uses' => 'DashboardController@drillDown'));
    Route::get('dashboard/drilldown/list/job', array('as' => 'dashboard.drilldown.job_list', 'uses' => 'DashboardController@getJobList'));
    Route::get('dashboard/drilldown/list/candidate-certificates', array('as' => 'dashboard.drilldown.candidate_certificate_list', 'uses' => 'DashboardController@getCandidateCertificateList'));

    /* Dashboard - end */

    /** need review - below items */

    Route::get('candidate/get-candidate', array('as' => 'candidate.get-candidate', 'uses' => 'CandidateController@getCandidate'));

    Route::post('candidate/archive', array('as' => 'candidate.archive', 'uses' => 'CandidateController@archive'));

    Route::get('candidate/job/{cand_job_id}/print-view', array('as' => 'candidate-job.print-view', 'uses' => 'CandidateController@printViewCandidateJob'));

    Route::get('candidate/geoCode', array('as' => 'candidate.geoCode', 'uses' => 'JobApplicationController@geoCode'));
    Route::get('customer/geoCode', array('as' => 'customer.geoCode', 'uses' => 'CustomerController@geoCode'));

    /*Candidate Schedule Summary - Start*/
    Route::middleware(['permission:candidate-schedule'])->group(function () {
        Route::get('candidate/schedule/customer/{customer_id?}/{requirement_id?}/{customer_contract_type?}/{security_clearence_id?}', array('as' => 'candidate.schedule', 'uses' => 'CandidateScheduleController@schedule'));
        Route::get('candidate/schedule/availablelist', array('as' => 'schedules.list', 'uses' => 'CandidateScheduleController@getEmployeesBasedOnSchedule'));
        Route::get('candidate/schedule/distance-travel-time-by-coordinates', array('as' => 'candidate.distance_travel_time_by_coordinates', 'uses' => 'CandidateScheduleController@distanceWithTimeByPositionCoordinates'));
    });

    Route::get('candidate/schedule/checkavailability/{id}/{requirement_id}', array('as' => 'checkavailability', 'uses' => 'CandidateScheduleController@getAvailabilityandUnavailabilityDates'));

    Route::get('candidate/stc/employee-summary/list/{employeeId?}/{spare?}', array('as' => 'stc.employee-summary.list', 'uses' => 'CandidateScheduleController@stcEmployeeSummaryList'));
    Route::get('candidate/stc/employee-summary', array('as' => 'stc.employee-summary', 'uses' => 'CandidateScheduleController@stcEmployeeSummary'));

    Route::middleware(['permission:candidate-schedule-summary'])->group(function () {
        Route::get('candidate/schedule/summary', array('as' => 'stc.summary', 'uses' => 'CandidateScheduleController@scheduleSummary'));
    });

    Route::get('candidate/schedule/{type}/requirements/{client_id?}', array('as' => 'scheduleRequirement.list', 'uses' => 'CandidateScheduleController@getscheduleRequirementList'));
    Route::post('candidate/schedule/requirements', array('as' => 'schedule.requirements', 'uses' => 'CandidateScheduleController@storeScheduleRequirements'));
    Route::post('candidate/schedule/updatetimeoff', array('as' => 'schedule.requirements.updatetimeoff', 'uses' => 'CandidateScheduleController@setScheduleRequirementstimeoff'));
    Route::get('candidate/schedule/requirement/{id}/details', array('as' => 'candidate.scheduleRequirementDetails', 'uses' => 'CandidateScheduleController@getRequirementDetails'));
    Route::post('candidate/schedule/update', array('as' => 'schedule.update', 'uses' => 'CandidateScheduleController@updateScheduleRequirements'));

    Route::get('candidate/schedule/stcprojectlist', array('as' => 'candidate.stcprojectlist', 'uses' => 'CandidateScheduleController@projectList'));
    Route::get('candidate/schedule/getstcprojectdetails', array('as' => 'schedule.getCustomer', 'uses' => 'CandidateScheduleController@getCustomer'));
    Route::get('candidate/schedule/{project_id}/{requirement_id}/details', array('as' => 'stc.details', 'uses' => 'CandidateScheduleController@scheduleDetails'));
    Route::get('candidate/schedule/eventlog/{project_id}/{requirement_id}', array('as' => 'stcdetails.list', 'uses' => 'CandidateScheduleController@scheduleEventLog'));

    Route::post('candidate/schedule/eventlog', array('as' => 'candidate.event_log_save', 'uses' => 'CandidateScheduleController@eventLogSave'));
    Route::get('candidate/schedule-event-log/{requirement_id}/{shift_id}/{user_id}', array('as' => 'candidate.eventLog', 'uses' => 'CandidateScheduleController@eventLogForm'));
    /*Candidate Schedule Summary - End*/

    /* Short term Contract - Start */
    Route::middleware(['permission:create-stc-customer'])->group(function () {
        Route::get('stc/create', array('as' => 'stc.create', 'uses' => 'ShortTermContractsController@create'));

        Route::post('stc/store', array('as' => 'stc.store', 'uses' => 'ShortTermContractsController@store'));
    });

    Route::middleware(['permission:view_openshift'])->group(function () {
        Route::get('openshift', array('as' => 'openshift', 'uses' => 'OpenShiftApprovalController@index'));
        Route::get('openshift/list/{checked?}/{client_id?}', array('as' => 'openshift.list', 'uses' => 'OpenShiftApprovalController@getList'));
        Route::get('openshift/details/{id}', array('as' => 'openshift.details', 'uses' => 'OpenShiftApprovalController@details'));
        Route::get('openshift/delete/{requirement_id}/{shift_id}/{user_id}', array('as' => 'openshift.delete', 'uses' => 'OpenShiftApprovalController@deleteAlreadyApproved'));
        Route::get('openshift/{requirement_id}/plot-in-map', array('as' => 'openshift.plot-in-map', 'uses' => 'OpenShiftApprovalController@plotOpenShiftMap'));
        Route::get('openshift/mail/{requirement_id}/{shift_id}/{user_id}/{status}', array('as' => 'openshift.mail', 'uses' => 'OpenShiftApprovalController@sendMail'));
        Route::get('openshift/shift-availability', array('as' => 'openshift.shift-availability', 'uses' => 'OpenShiftApprovalController@shiftAvailability'));
    });

    Route::middleware(['permission:list-stc-customers'])->group(function () {
        Route::name('stc')->get('stc', 'ShortTermContractsController@index');
        Route::get('stc/list', array('as' => 'stc.list', 'uses' => 'ShortTermContractsController@getList'));
        Route::get('stc/destroy', array('as' => 'stc.destroy', 'uses' => 'ShortTermContractsController@destroy'));
        Route::get('stc/{id}/edit', array('as' => 'stc.edit', 'uses' => 'ShortTermContractsController@edit'));
        Route::get('stc/bonuslist', array('as' => 'stc.bonuslist', 'uses' => 'StcBonusController@bonusPrograms'));
        Route::get('stc/bonus/{id?}', array('as' => 'stc.bonus', 'uses' => 'StcBonusController@bonusModel'));
        Route::get('stc/bonussettings/{id?}', array('as' => 'stc.bonussettings', 'uses' => 'StcBonusController@bonusSettings'));
        Route::post('stc/savebonussettings', array('as' => 'stc.savebonussettings', 'uses' => 'StcBonusController@saveBonussettings'));
        Route::post('stc/processbonusprogram', array('as' => 'stc.processbonusprogram', 'uses' => 'StcBonusController@saveProcessbonusprogram'));
    });
    /* Short term Contract - End */
    /*Employye exit interview start*/
    //    Route::get('employee/exittermination', array('as' => 'employee.exittermination', 'uses' => 'EmployeeExitTerminationController@getEmployeeExitInterview'));
    // Route::post('employee/exitinterview/store', array('as' => 'employee.exitinterview.store', 'uses' => 'EmployeeExitTerminationController@save'));
    /*Employee exit interview end*/

    Route::get('employee/mapping', array('as' => 'employee.mapping', 'uses' => 'EmployeeController@getEmployeesMap'));
    Route::get('employee/rating/{id}', array('as' => 'employee.performance-view', 'uses' => 'EmployeeController@getPerfomanceLog'));
    Route::post('employee/rating', array('as' => 'employee.rating', 'uses' => 'EmployeeController@storePerfomance'));
    Route::get('employee/course-list/{id}', array('as' => 'employee.course-list', 'uses' => 'EmployeeController@getCourseAllocation'));
    Route::get('employee/ratings-summary/{id}', array('as' => 'employee.ratings-summary', 'uses' => 'CandidateScheduleController@getEmployeeRatings'));
    Route::get('employee/ratings/getPolicy/{id}', array('as' => 'employee.ratings-getPolicy', 'uses' => 'CandidateScheduleController@getPolicyByRating'));
    Route::get('employee/ratings/getPolicy/{id}', array('as' => 'employee.ratings-getPolicy', 'uses' => 'CandidateScheduleController@getPolicyByRating'));
    Route::get('employee/ratings/timesheetapproval/{id}', array('as' => 'employee.timesheet-approval-rating.list', 'uses' => 'EmployeeController@employeeTimesheetApprovalRatingList'));
    Route::get('employee/ratings/timesheetApprovalByPayperiod/{payperiod_id}/{emp_id}', array('as' => 'timesheetapprovalbypayperiod.details', 'uses' => 'EmployeeController@getTimesheetapprovalbypayperiod'));
    Route::get('employee/ratings/timesheetapproval/destroy/{id}', array('as' => 'employee.timesheet-approval-rating.destroy', 'uses' => 'EmployeeController@timeSheetApprovalRatingDestroy'));
    /*Employee whistleblower - start */
    Route::delete('client-rating/{id}', array('as' => 'client-rating.delete', 'uses' => 'EmployeeController@deleteClientRating'));
    Route::delete('manager-rating/{id}', array('as' => 'manager-rating.delete', 'uses' => 'EmployeeController@deleteManagerRating'));


    /* Employee Exit Interview */
    Route::middleware(['permission:create_exit_interview|create_all_exit_interview'])->group(function () {
        Route::post('employee/exitinterview/store', array('as' => 'employee.exitinterview.store', 'uses' => 'EmployeeExitInterviewController@save'));
        Route::get('employee/exittermination', array('as' => 'employee.exittermination', 'uses' => 'EmployeeExitInterviewController@getEmployeeExitInterview'));
    });
    Route::middleware(['permission:create_exit_interview|create_all_exit_interview|view_all_exit_interview|view_exit_interview'])->group(function () {
        Route::get('employee/exittermination/summary', array('as' => 'employee.exitterminationsummary', 'uses' => 'EmployeeExitInterviewController@getEmployeeExitInterviewSummary'));
        Route::get('employee/exittermination/summarylist', array('as' => 'employee.exitterminationsummarylist', 'uses' => 'EmployeeExitInterviewController@getEmployeeExitInterviewSummaryList'));
    });
    Route::get('exitinterviewalloction/list/{customer_id?}', array('as' => 'exitinterviewalloction.list', 'uses' => 'EmployeeExitInterviewController@getAllocationList'));
    // /*Employee exit interview end*/

    /* Employee whistleblower start */
    Route::middleware(['permission:view_employee_whistleblower|view_all_whistleblower|view_allocated_whistleblower|create_employee_whistleblower|create_all_whistleblower|create_allocated_whistleblower'])->group(function () {
        Route::get('employee/whistleblower', array('as' => 'employee.whistleblower', 'uses' => 'EmployeeWhistleblowerController@index'));
        Route::get('employee/whistleblower/summarylist', array('as' => 'employee.whistleblowersummarylist', 'uses' => 'EmployeeWhistleblowerController@getEmployeeWhistleblowerSummaryList'));
        Route::get('employee/whistleblower/{id}', array('as' => 'employee.whistleblower-single', 'uses' => 'EmployeeWhistleblowerController@edit'));
    });
    Route::middleware(['permission:create_all_whistleblower|create_allocated_whistleblower|create_employee_whistleblower'])->group(function () {
        Route::post('employee/whistleblower/store', array('as' => 'employee.whistleblower.store', 'uses' => 'EmployeeWhistleblowerController@store'));
    });
    /* Employee whistleblower ends */

    Route::middleware(['permission:candidate_transition_process'])->group(function () {
        Route::get('candidate/conversion', array('as' => 'candidate.conversion', 'uses' => 'CandidateController@candidateConversion'));
        Route::get('candidate/conversionlist', array('as' => 'conversion.list', 'uses' => 'CandidateController@candidateConversionList'));
        Route::get('candidate/show/{id}', array('as' => 'candidate.show', 'uses' => 'CandidateController@showDetails'));
        Route::post('candidate/{module}/store', array('as' => 'candidateEmployee.store', 'uses' => 'CandidateController@Employeestore'));
        Route::post('candidate/{candidate_id}/profile-image-upload', array('as' => 'candidate.profile-image-upload', 'uses' => 'CandidateController@uploadProfileImage'));
    });

    Route::middleware(['permission:terminate_candidate_application'])->group(function () {
        Route::post('candidate/terminate/{candidate_id}', array('as' => 'candidate.terminate', 'uses' => 'CandidateController@terminateCandidateApplication'));
    });

    Route::middleware(['role:super_admin'])->group(function () {
        Route::post('candidate/reactivate/{candidate_id}/{reset?}', array('as' => 'candidate.reactivate', 'uses' => 'CandidateController@reactivateCandidateApplication'));
    });

    Route::get('employee/timeofflist/{id}', array('as' => 'employeetimeoff-list', 'uses' => 'EmployeeController@employeeTimeOffList'));

    Route::get('candidate/multifill', array('as' => 'multifill.tablegenerate', 'uses' => 'CandidateScheduleController@multifillGenerateRows'));
    Route::get('multifill/destroy/{shift_id}', array('as' => 'multifill.destroy', 'uses' => 'CandidateScheduleController@deleteCandidateforMultifill'));
    Route::get('multifill/delete/shift/{shift_id}', array('as' => 'multifill.delete-shift', 'uses' => 'CandidateScheduleController@deleteMultifillShift'));
    Route::get('multifill/delete/shift/{shift_id}', array('as' => 'multifill.delete-shift', 'uses' => 'CandidateScheduleController@deleteMultifillShift'));
    Route::get('candidate/schedule-overview', array('as' => 'candidate.scheduleOverview', 'uses' => 'CandidateScheduleController@getScheduleOverview'));
    Route::post('multifill/changeparent', array(
        'as' => 'multifill.changeparent',
        'uses' => 'CandidateScheduleController@updateMultiFillParent'
    ));

    Route::get('employeeschedule/entry', array('as' => 'employee.scheduleEntry', 'uses' => 'EmployeeScheduleController@entryform'));
    Route::post('employeeschedule/store', array('as' => 'employee.scheduleStore', 'uses' => 'EmployeeScheduleController@store'));
    Route::post('employeeschedule/get', array('as' => 'employee.getSchedule', 'uses' => 'EmployeeScheduleController@getSchedule'));
    Route::post('employeeschedule/unavailability', array('as' => 'employee.unAvailabilityStore', 'uses' => 'EmployeeUnavailabilityController@unavailablility'));
    Route::get('employeeschedule/unavailability/list', array('as' => 'employee.unAvailabilityList', 'uses' => 'EmployeeUnavailabilityController@unavailablilityList'));
    Route::get('employeeschedule/unavailability/edit/{id}', array('as' => 'employeeUnavailability.edit', 'uses' => 'EmployeeUnavailabilityController@edit'));
    Route::get('employeeschedule/unavailability/destroy/{id}', array('as' => 'unavailability.destroy', 'uses' => 'EmployeeUnavailabilityController@deleteUnavailability'));
    Route::get('employeeschedule/list/{type}', array('as' => 'employeeList.list', 'uses' => 'EmployeeScheduleController@employeeList'));

    Route::get('recruitment-analytics', array('as' => 'recruitment-analytics.index', 'uses' => 'RecruitmentAnalyticsDashboardController@index'));
    Route::get('recruitment-analytics/tabs', array('as' => 'recruitment-analytics.tabs', 'uses' => 'RecruitmentAnalyticsDashboardController@getDashboardTabs'));
    Route::get('recruitment-analytics/tab-details', array('as' => 'recruitment-analytics.tab-details', 'uses' => 'RecruitmentAnalyticsDashboardController@getDashboardTabDetails'));
    Route::get('recruitment-analytics/job-requisitions', array('as' => 'recruitment-analytics.job-requisitions', 'uses' => 'RecruitmentAnalyticsDashboardController@getJobRequisitionAnalytics'));
    Route::get('recruitment-analytics/position-by-region', array('as' => 'recruitment-analytics.position-by-region', 'uses' => 'RecruitmentAnalyticsDashboardController@getPositionByRegionAnalytics'));
    Route::get('recruitment-analytics/highest-turnover', array('as' => 'recruitment-analytics.highest-turnover', 'uses' => 'RecruitmentAnalyticsDashboardController@getHighestTurnoverAnalytics'));
    Route::get('recruitment-analytics/position-by-reasons', array('as' => 'recruitment-analytics.position-by-reasons', 'uses' => 'RecruitmentAnalyticsDashboardController@getPositionByReasonsAnalytics'));
    Route::get('recruitment-analytics/wage-by-region', array('as' => 'recruitment-analytics.wage-by-region', 'uses' => 'RecruitmentAnalyticsDashboardController@getWageByRegionAnalytics'));
    Route::get('recruitment-analytics/candidates', array('as' => 'recruitment-analytics.candidates', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidatesAnalytics'));
    Route::get('recruitment-analytics/candidates-regions', array('as' => 'recruitment-analytics.candidates-regions', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidatesRegions'));
    Route::get('recruitment-analytics/candidates-certificates', array('as' => 'recruitment-analytics.candidates-certificates', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidatesCertificates'));
    Route::get('recruitment-analytics/candidates-experiences', array('as' => 'recruitment-analytics.candidates-experiences', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidatesExperiences'));
    Route::get('recruitment-analytics/candidates-experiences-regions', array('as' => 'recruitment-analytics.candidates-experiences-regions', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidatesExperiencesByRegions'));
    Route::get('recruitment-analytics/wage-expectations-by-region', array('as' => 'recruitment-analytics.wage-expectations-by-region', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidateWageExpectationsByRegion'));
    Route::get('recruitment-analytics/wage-expectations-by-position', array('as' => 'recruitment-analytics.wage-expectations-by-position', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidateWageExpectationsByPosition'));
    Route::get('recruitment-analytics/wage-by-competitor', array('as' => 'recruitment-analytics.wage-by-competitor', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidateWageByCompetitor'));
    Route::get('recruitment-analytics/candidate-resident-status', array('as' => 'recruitment-analytics.candidate-resident-status', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidateResidentStatusAnalytics'));
    Route::get('recruitment-analytics/candidate-military-experience', array('as' => 'recruitment-analytics.candidate-military-experience', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidateMilitaryExperienceAnalytics'));
    Route::get('recruitment-analytics/guard-drivers-license', array('as' => 'recruitment-analytics.guard-drivers-license', 'uses' => 'RecruitmentAnalyticsDashboardController@getGuardDriversLicense'));
    Route::get('recruitment-analytics/access-to-public-transit', array('as' => 'recruitment-analytics.access-to-public-transit', 'uses' => 'RecruitmentAnalyticsDashboardController@getAccessToPublicTransit'));
    Route::get('recruitment-analytics/limited-transportation', array('as' => 'recruitment-analytics.limited-transportation', 'uses' => 'RecruitmentAnalyticsDashboardController@getLimitedTransportation'));
    Route::get('recruitment-analytics/level-of-language-fluency-english', array('as' => 'recruitment-analytics.level-of-language-fluency-english', 'uses' => 'RecruitmentAnalyticsDashboardController@getLevelOfLanguageFluencyEnglish'));
    Route::get('recruitment-analytics/level-of-language-fluency-french', array('as' => 'recruitment-analytics.level-of-language-fluency-french', 'uses' => 'RecruitmentAnalyticsDashboardController@getLevelOfLanguageFluencyFrench'));
    Route::get('recruitment-analytics/candidates-skills-computer', array('as' => 'recruitment-analytics.candidates-skills-computer', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidatesComputerSkill'));
    Route::get('recruitment-analytics/candidates-skills-soft', array('as' => 'recruitment-analytics.candidates-skills-soft', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidateSoftSkills'));
    Route::get('recruitment-analytics/employment-entities', array('as' => 'recruitment-analytics.employment-entities', 'uses' => 'RecruitmentAnalyticsDashboardController@getEmploymentEntities'));
    Route::get('recruitment-analytics/fired-vs-convicted-candidates', array('as' => 'recruitment-analytics.fired-vs-convicted-candidates', 'uses' => 'RecruitmentAnalyticsDashboardController@getFiredVsConvictedCandidates'));
    Route::get('recruitment-analytics/wage-by-industry-sector', array('as' => 'recruitment-analytics.wage-by-industry-sector', 'uses' => 'RecruitmentAnalyticsDashboardController@getWageByIndustrySector'));
    Route::get('recruitment-analytics/planned-ojt', array('as' => 'recruitment-analytics.planned-ojt', 'uses' => 'RecruitmentAnalyticsDashboardController@getWidgetPlannedOJT'));
    Route::get('recruitment-analytics/candidates-by-career-interest-in-cgl', array('as' => 'recruitment-analytics.candidates-by-career-interest-in-cgl', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidatesByCareerInterestInCgl'));
    Route::get('recruitment-analytics/candidates-by-average-score', array('as' => 'recruitment-analytics.candidates-by-average-score', 'uses' => 'RecruitmentAnalyticsDashboardController@getCandidatesByAverageScore'));
    Route::get('recruitment-analytics/loading-documents', array('as' => 'recruitment-analytics.loading-documents', 'uses' => 'RecruitmentAnalyticsDashboardController@getLoadingDocuments'));
    Route::get('recruitment-analytics/average-cycle-time', array('as' => 'recruitment-analytics.average-cycle-time', 'uses' => 'RecruitmentAnalyticsDashboardController@getAverageCycleTime'));

    Route::get('employeeFeedback/view', array('as' => 'employee.employeeFeedback', 'uses' => 'EmployeeFeedbackController@index'));
    Route::post('employeeFeedback/feedbackdata', ["as" => "employee.feedbackdata", "uses" => 'EmployeeFeedbackController@listFeedbacks']);
    Route::get('employeeFeedback/viewDetailed/{id}', array('as' => 'employee.employeeFeedbackDetailed', 'uses' => 'EmployeeFeedbackController@employeeFeedbackDetailed'));
    Route::post('employeeFeedback/savefeedbackapproval', ["as" => "employee.savefeedbackapproval", "uses" => 'EmployeeFeedbackController@saveFeedbackApproval']);
    Route::get('employeeFeedback/viewmap/{id}', array('as' => 'employee.employeeFeedbackMap', 'uses' => 'EmployeeFeedbackController@viewEmployeeMap'));

    Route::get('employeeSurvey/view', array('as' => 'employee.employeeSurveys', 'uses' => 'EmployeeSurveyViewController@index'));
    Route::post('/employeesurveydata', ["as" => "employee.surveydata", "uses" => 'EmployeeSurveyViewController@getSurveyData']);
    Route::post('/employeesurveydatadetailed', ["as" => "employee.surveyDataDetailed", "uses" => 'EmployeeSurveyViewController@getSurveyDataDetailed']);
    Route::post('/employeesurveyplotdata', ["as" => "employeesurvey.plotdata", "uses" => 'EmployeeSurveyViewController@plotData']);
    Route::get('employeeSurvey/view/{id}', array('as' => 'employeeSurvey.view', 'uses' => 'EmployeeSurveyViewController@detailedView'));
    Route::post('/getsurveyblock', ["as" => "employeesurvey.surveyblock", "uses" => 'EmployeeSurveyViewController@getSurveyblock']);
    Route::post('/employeesurveyplotgraph', ["as" => "employeesurvey.plotgraph", "uses" => 'EmployeeSurveyViewController@plotGraph']);
});
