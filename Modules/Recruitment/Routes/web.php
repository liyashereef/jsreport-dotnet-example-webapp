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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'recruitment',], function () {
    Route::get('/', 'RecruitmentController@index');


    /* Candidate Credentials - start */
    Route::middleware(['permission:rec-candidate-credential|rec-create-candidate-credential|rec-edit-candidate-credential|rec-delete-candidate-credential'])->group(function () {
        Route::get('candidate-credentials', array('as' => 'recruitment.candidate-credentials', 'uses' => 'RecCandidateController@candidateCredentials'));
        Route::get('candidate-credentials/list', array('as' => 'recruitment.candidate-credentials.list', 'uses' => 'RecCandidateController@getCandidateCredentialsList'));
        // Route::get('candidate-credentials/single/{id}', array('as' => 'recruitment.candidate-credentials.single', 'uses' => 'RecCandidateCredentialsController@getSingle'));
        // Route::post('candidate-credentials/store', array('as' => 'recruitment.candidate-credentials.store', 'uses' => 'RecCandidateCredentialsController@store'));
        // Route::get('candidate-credentials/destroy/{id}', array('as' => 'recruitment.candidate-credentials.destroy', 'uses' => 'RecCandidateCredentialsController@destroy'));
    });
    Route::middleware(['permission:rec-view-allocated-candidates-tracking'])->group(function () {
        Route::get('candidate-tracking/list', array('as' => 'recruitment.candidate-tracking.list', 'uses' => 'RecCandidateController@getCandidateTrackingList'));
    });


    Route::get('candidate-uniform-details/{candidate_id}', array('as' => 'recruitment.candidate-uniform-details', 'uses' => 'RecCandidateController@getUniformDetailsOfCandidate'));
    Route::get('candidate-reset-password/show/{id}', array('as' => 'recruitment.candidate-reset-password-show', 'uses' => 'RecCandidateController@showResetPasswordTemplate'));
    Route::post('candidate-reset-password/sendMail', array('as' => 'recruitment.candidate-reset-password-sendmail', 'uses' => 'RecCandidateController@sendPasswordResetMail'));


    Route::get('candidate-match-score/{candidate_id}/{job_id}', array('as' => 'recruitment.candidate-match-score.show', 'uses' => 'RecCandidateSelectionController@showCandidateMatchScore'));
    Route::post('candidate/selection/status', array('as' => 'recruitment.candidate-selection-update', 'uses' => 'RecCandidateSelectionController@updateCandidateSelectionStatus'));
    Route::middleware(['permission:rec-create-candidate-credential|rec-edit-candidate-credential'])->group(function () {
        Route::post('candidate-credentials/store', array('as' => 'recruitment.candidate-credentials.store', 'uses' => 'RecCandidateController@storeCandidateCredentials'));
    });

    Route::middleware(['permission:rec-edit-candidate-credential'])->group(function () {
        Route::get('candidate-credentials/single/{id}', array('as' => 'recruitment.candidate-credentials.single', 'uses' => 'RecCandidateController@getSingleCandidateCredential'));
    });

    Route::middleware(['permission:rec-delete-candidate-credential'])->group(function () {
        Route::get('candidate-credentials/destroy/{id}', array('as' => 'recruitment.candidate-credentials.destroy', 'uses' => 'RecCandidateController@destroyCandidateCredential'));
    });

    Route::middleware(['permission:rec-view-candidate-training'])->group(function () {
        Route::get('candidate/training', array('as' => 'recruitment.candidate-training', 'uses' => 'RecCandidateController@candidateTraining'));
        Route::get('candidate/traininglist', array('as' => 'recruitment.candidate-traininglist', 'uses' => 'RecCandidateController@candidateTrainingList'));

    });

    /* Candidate Credentials - end */

    /* Candidate Conversion - Start */
    Route::middleware(['permission:rec_candidate_transition_process'])->group(function () {
        Route::get('candidate/conversion', array('as' => 'recruitment.candidate.conversion', 'uses' => 'RecCandidateController@candidateConversion'));
        Route::get('candidate/conversionlist', array('as' => 'recruitment.conversion.list', 'uses' => 'RecCandidateController@candidateConversionList'));
        Route::get('candidate/show/{id}', array('as' => 'recruitment.candidate.show', 'uses' => 'RecCandidateController@showDetails'));
        Route::post('candidate/{module}/store', array('as' => 'recruitment.candidateEmployee.store', 'uses' => 'RecCandidateController@Employeestore'));
        Route::post('candidate/{candidate_id}/profile-image-upload', array('as' => 'recruitment.candidate.profile-image-upload', 'uses' => 'RecCandidateController@uploadProfileImage'));
    });

    Route::name('recruitment.document.store')->post('document/store', 'RecCandidateController@documentStore');
    /* Candidate Conversion - End */

    /* Candidate Summary - Start */
    Route::middleware(['permission:rec-candidate-screening-summary|rec-view-allocated-candidates-summary'])->group(function () {
        Route::get('candidate/summary', array('as' => 'recruitment.candidate.summary', 'uses' => 'RecCandidateController@candidateSummary'));
        Route::get('candidate/summmarylist', array('as' => 'recruitment.candidate.summarylist', 'uses' => 'RecCandidateController@candidateSummaryList'));
        //Excel Export
        Route::get('candidate/candidate-export', array('as' => 'recruitment.candidate.candidate-export', 'uses' => 'RecCandidateController@candidateExport'));
        Route::get('candidate/candidateGeomapping-export', array('as' => 'recruitment.candidate.candidateGeomapping-export', 'uses' => 'RecCandidateController@candidateGeomappingExport'));
        Route::post('candidate/candidateGeomapping-store-candidate-sessionalData', array('as' => 'recruitment.candidate.candidateGeomapping-storeCandidateSessionalData', 'uses' => 'RecCandidateController@storeCandidateSessionalData'));

        // Print
        Route::get('candidate/job/{cand_job_id}/print-view', array('as' => 'recruitment.candidate-job.print-view', 'uses' => 'RecCandidateController@printViewCandidateJob'));
    });
    Route::get('candidate/selection', array('as' => 'recruitment.candidate.selection', 'uses' => 'RecCandidateSelectionController@candidateSelection'));
    Route::get('candidate/selection/list', array('as' => 'recruitment.candidate.selection.list', 'uses' => 'RecCandidateSelectionController@candidateSelectionList'));
    Route::get('candidate/force-attachemnt/download/{file}', array('as' => 'reccandidate-force-document.download', 'uses' => 'RecCandidateController@downloadForceDocument'));

    Route::get('candidate/compare/{candidate_ids}', array('as' => 'recruitment.candidate.compare', 'uses' => 'RecCandidateController@compare'));

    Route::middleware(['permission:rec-candidate-approval'])->group(function () {
        Route::post('candidate/primary-screening', array('as' => 'recruitment.candidate.primary-screening', 'uses' => 'RecCandidateController@updateCandidateJobStatus'));
    });

    Route::middleware(['permission:rec-candidate-rate-screening-question-answers'])->group(function () {
        Route::post('candidate/review/answers', array('as' => 'recruitment.candidate.review.answers', 'uses' => 'RecCandidateController@reviewScreeningAnswers'));
    });

    Route::middleware(['permission:rec-edit-candidate'])->group(function () {
        Route::get('candidate/{candidate_id}/edit', array('as' => 'recruitment.candidate.edit', 'uses' => 'RecCandidateController@editCandidateJob'));
    });
    /* Candidate Summary - End */

    /* Candidate Summary Edit - Start */
    Route::get('apply/{id}/view', array('as' => 'recruitment.applyjob.view', 'uses' => 'RecJobApplicationController@viewApplication'));
    Route::post('apply/attachment', array('as' => 'recruitment.applyjob.attachment', 'uses' => 'RecJobApplicationController@storeAttachment'));
    Route::post('apply/store', array('as' => 'recruitment.applyjob.store', 'uses' => 'RecJobApplicationController@store'));
    Route::post('apply/store/screening', array('as' => 'recruitment.applyjob.storescreeningquestion', 'uses' => 'RecJobApplicationController@store_screening_questions'));
    Route::post('apply/store/uniform', array('as' => 'recruitment.applyjob.storeUniform', 'uses' => 'RecJobApplicationController@store_uniform_measures'));
    Route::post('apply/store/personality', array('as' => 'recruitment.applyjob.storepersonality', 'uses' => 'RecJobApplicationController@store_personality'));
    Route::post('apply/store/competency-matrix', array('as' => 'recruitment.applyjob.competencymatrix', 'uses' => 'RecJobApplicationController@store_competency_matrix'));
    Route::get('get-address-form', array('as' => 'recruitment.previousaddress.add', function () {
        return view('recruitment::job-application.partials.profile.previous-address');
    }));
    Route::get('get-positon-form', array('as' => 'recruitment.position.add', function () {
        return view('recruitment::job-application.partials.profile.employement-history');
    }));
    Route::get('get-reference-form', array('as' => 'recruitment.reference.add', function () {
        return view('recruitment::job-application.partials.profile.references');
    }));
    Route::get('get-education-form', array('as' => 'recruitment.education.add', function () {
        return view('recruitment::job-application.partials.profile.education');
    }));
    Route::get('get-language-form', array('as' => 'recruitment.language.add', 'uses' => 'RecJobApplicationController@getOtherlanguages'));
    Route::get('candidate/remove-attachment/{candidate_id}/{attachment_id}', array('as' => 'recruitment.candidate.remove-attachment', 'uses' => 'RecCandidateController@removeCandidateAttachment'));
    Route::get('candidate/remove-document/{candidate_id}/{document_id}', array('as' => 'recruitment.candidate.remove-document', 'uses' => 'RecCandidateController@removeCandidateDocument'));

    /* Candidate Summary Edit - End */

    /* Candidate Process Steps - start */
    Route::get('candidate-process-step', array('as' => 'recruitment.candidate-process-step', 'uses' => 'RecCandidateProcessStepsController@index'));
    Route::get('candidate/track/{id}', array('as' => 'recruitment.candidate.track', 'uses' => 'RecCandidateProcessStepsController@track'));
    Route::post('candidate-track/store', array('as' => 'recruitment.candidate-track.store', 'uses' => 'RecCandidateProcessStepsController@saveTracking'));
    Route::get('candidate-track/destroy/{tracking_id}/{candidate_id}', array('as' => 'recruitment.candidate-track-delete', 'uses' => 'RecCandidateProcessStepsController@deleteTracking'));
    /* Candidate Process Steps - end */

    /* Candidate Uniform Shippment Details - Start */
    Route::get('candidate-uniform-shippment-detail', array('as' => 'recruitment.uniform-shippment-detail', 'uses' => 'RecCandidateUniformShippmentDetailController@index'));
    Route::get('candidate-uniform-shippment-detail/list', array('as' => 'recruitment.candidate-uniform-shippment-detail.list', 'uses' => 'RecCandidateUniformShippmentDetailController@getList'));
    Route::post('candidate-uniform-shippment-detail/saveStatus', array('as' => 'recruitment.candidate-uniform-shippment-detail.saveStatus', 'uses' => 'RecCandidateUniformShippmentDetailController@saveStatus'));
    /* Candidate Uniform Shippment Details - End */
    Route::get('recruitment.uniform.getKitDetails/{id}/{candidate_id}', array('as' => 'recruitment.uniform.getKitDetails', 'uses' => 'RecCandidateUniformShippmentDetailController@getKitDetails'));

    Route::get('job/view-description/{job_id}', array('as' => 'recruitment-job.view-description', 'uses' => 'RecJobController@viewJobDescription'));
    Route::middleware(['permission:rec-create-job|rec-edit-job|rec-delete-job|rec-archive-job|rec-job-approval|rec-hr-tracking|rec-job-attachement-settings|rec-list-jobs-from-all|rec-candidate-mapping|rec-view_all_candidates_candidate_geomapping|rec-view-allocated-job-requisitions|rec-view-allocated-candidates-geomapping'])->group(function () {
        Route::name('recruitment-job')->get('recruitment-job', 'RecJobController@index');
        Route::get('recruitment-job/list/{status?}', array('as' => 'recruitment-job.list', 'uses' => 'RecJobController@getList'));
        Route::get('job/mapping', array('as' => 'recruitment-job.mapping', 'uses' => 'RecJobController@jobMapping'));
    });
    Route::middleware(['permission:rec-create-job'])->group(function () {
        Route::get('job/create', array('as' => 'recruitment-job.create', 'uses' => 'RecJobController@create'));
    });

    Route::middleware(['permission:rec-edit-job'])->group(function () {
        Route::get('job/edit/{id}', array('as' => 'recruitment-job.edit', 'uses' => 'RecJobController@edit'));
    });

    Route::middleware(['permission:rec-edit-job|rec-create-job'])->group(function () {
        Route::post('job/store', array('as' => 'recruitment-job.store', 'uses' => 'RecJobController@store'));
        Route::get('document-allocation/single-customer/{cid}', array('as' => 'document-allocation.getCustomerDocument', 'uses' => 'RecJobController@customerDocumentAllocation'));
    });


    Route::middleware(['permission:rec-archive-job'])->group(function () {
        Route::post('job/archive', array('as' => 'recruitment-job.archive', 'uses' => 'RecJobController@archiveJob'));
    });

    Route::get('candidate-selection', array('as' => 'recruitment.candidate-selection', 'uses' => 'RecCandidateProcessStepsController@index'));

    Route::get('candidate/jobs/{customer_id?}', array('as' => 'recruitment.customer.getJob', 'uses' => 'RecJobController@getJobsUnderCustomer'));

    /* Candidate Geomapping - start */
    Route::middleware(['permission:rec-candidate-mapping|rec-view-allocated-candidates-geomapping'])->group(function () {
        Route::get('candidate/mapping', array('as' => 'recruitment.candidate.mapping', 'uses' => 'RecCandidateController@mapping'));
        Route::get('candidate/{job_id}/plot-in-map-with-customer', array('as' => 'recruitment.candidate.plot-in-map-with-customer', 'uses' => 'RecCandidateController@plotJobCandidatesMap'));
    });

    Route::get('candidate/{candidate_id}/view', array('as' => 'recruitment.candidate.view', 'uses' => 'RecCandidateController@viewCandidate'));
    Route::get('candidate/onboarding/{candidate_id}/view', array('as' => 'recruitment.candidate-onboarding.view', 'uses' => 'RecCandidateController@viewCandidateOnboarding'));
    Route::post('job/hr-tracking/{job_id}', array('as' => 'recruitment-job.hr-tracking', 'uses' => 'RecJobController@hrTrackingStore'));
    // Route::get('candidate/{candidate_id}/{job_id}/track', array('as' => 'recruitment.candidate.track', 'uses' => 'RecCandidateController@trackCandidate'));
    /* Candidate Geomapping - end */
    Route::middleware(['permission:rec-delete-hr-tracking'])->group(function () {
        Route::get('job/remove-hr-tracking-step/{job_id}/{step_id}', array('as' => 'recruitment-job.remove-hr-tracking-step', 'uses' => 'RecJobController@hrTrackingRemove'));
        // Route::get('candidate/remove-hr-tracking-step/{job_id}/{candidate_id}/{step_id}', array('as' => 'candidate.remove-hr-tracking-step', 'uses' => 'CandidateController@hrTrackingRemove'));
    });


    Route::get('job/hr-tracking/{job_id}', array('as' => 'recruitment-job.hr-tracking', 'uses' => 'RecJobController@hrTracking'));
    Route::middleware(['permission:rec-job-approval'])->group(function () {
        Route::post('job/update-status/{job_id}', array('as' => 'recruitment-job.update-status', 'uses' => 'RecJobController@changeStatus'));
    });
    Route::get('job/view/{id}', array('as' => 'recruitment-job.view', 'uses' => 'RecJobController@viewJob'));

    Route::middleware(['permission:rec-job-attachement-settings'])->group(function () {
        Route::post('job/attachment-settings/{job_id}', array('as' => 'recruitment-job.attachment-settings', 'uses' => 'RecJobController@setMandatoryAttachements'));
    });

    Route::middleware(['permission:rec-job-tracking-summary'])->group(function () {
        Route::get('job/hr-tracking-summary', array('as' => 'recruitment-job.hr-tracking-summary', 'uses' => 'RecJobController@hrTrackingSummary'));
        Route::get('job/hr-tracking-summary-list', array('as' => 'recruitment-job.hr-tracking-summary.list', 'uses' => 'RecJobController@hrTrackingSummaryList'));
    });
});


Route::group(['middleware' => ['web', 'auth', 'permission:recruitment_masters']], function () {

    Route::prefix('admin/recruitment')->name('recruitment.')->namespace('Admin')->group(function () {
        /* Candidate Brand Awareness - start */
        Route::name('brand-awareness')->get('brand-awareness', 'RecBrandAwarenessController@index');
        Route::get('brand-awareness/list', array('as' => 'brand-awareness.list', 'uses' => 'RecBrandAwarenessController@getList'));
        Route::get('brand-awareness/lookupList', array('as' => 'brand-awareness.lookupList', 'uses' => 'RecBrandAwarenessController@lookupList'));
        Route::get('brand-awareness/single/{id}', array('as' => 'brand-awareness.single', 'uses' => 'RecBrandAwarenessController@getSingle'));
        Route::post('brand-awareness/store', array('as' => 'brand-awareness.store', 'uses' => 'RecBrandAwarenessController@store'));
        Route::get('brand-awareness/destroy/{id}', array('as' => 'brand-awareness.destroy', 'uses' => 'RecBrandAwarenessController@destroy'));
        /* Candidate Brand Awareness - end */

        /* Process Steps - start */
        Route::name('process-steps')->get('process-steps', 'RecProcessStepsController@index');
        Route::get('process-steps/list', array('as' => 'process-steps.list', 'uses' => 'RecProcessStepsController@getProcessStepsList'));
        Route::get('process-steps/single/{id}', array('as' => 'process-steps.single', 'uses' => 'RecProcessStepsController@getSingle'));
        Route::post('process-steps/store', array('as' => 'process-steps.store', 'uses' => 'RecProcessStepsController@store'));
        Route::get('process-steps/destroy/{id}', array('as' => 'process-steps.destroy', 'uses' => 'RecProcessStepsController@destroy'));
        /* Process Steps - end */

        /* English Rating - start */
        Route::name('english-rating')->get('english-rating', 'RecEnglishRatingLookupController@index');
        Route::get('english-rating/list', array('as' => 'english-rating.list', 'uses' => 'RecEnglishRatingLookupController@getList'));
        Route::get('english-rating/single/{id}', array('as' => 'english-rating.single', 'uses' => 'RecEnglishRatingLookupController@getSingle'));
        Route::post('english-rating/store', array('as' => 'english-rating.store', 'uses' => 'RecEnglishRatingLookupController@store'));
        Route::get('english-rating/destroy/{id}', array('as' => 'english-rating.destroy', 'uses' => 'RecEnglishRatingLookupController@destroy'));
        /* English Rating - end */

        /* Candidate Security Awareness - start */
        Route::name('security-awareness')->get('security-awareness', 'RecSecurityAwarenessController@index');
        Route::get('security-awareness/list', array('as' => 'security-awareness.list', 'uses' => 'RecSecurityAwarenessController@getList'));
        Route::get('security-awareness/lookupList', array('as' => 'security-awareness.lookupList', 'uses' => 'RecSecurityAwarenessController@lookupList'));
        Route::get('security-awareness/single/{id}', array('as' => 'security-awareness.single', 'uses' => 'RecSecurityAwarenessController@getSingle'));
        Route::post('security-awareness/store', array('as' => 'security-awareness.store', 'uses' => 'RecSecurityAwarenessController@store'));
        Route::get('security-awareness/destroy/{id}', array('as' => 'security-awareness.destroy', 'uses' => 'RecSecurityAwarenessController@destroy'));
        /* Candidate Security Awareness - end */

        /* experience lookups- start */
        Route::name('experience-lookups')->get('experience-lookups', 'RecExperienceLookupController@index');
        Route::get('experience-lookups/list', array('as' => 'experience-lookups.list', 'uses' => 'RecExperienceLookupController@getList'));
        Route::get('experience-lookups/single/{id}', array('as' => 'experience-lookups.single', 'uses' => 'RecExperienceLookupController@getSingle'));
        Route::post('experience-lookups/store', array('as' => 'experience-lookups.store', 'uses' => 'RecExperienceLookupController@store'));
        Route::get('experience-lookups/destroy/{id}', array('as' => 'experience-lookups.destroy', 'uses' => 'RecExperienceLookupController@destroy'));
        /* experience lookups - end */

        /* Rate Experiences - start */
        Route::name('rate-experiences')->get('rate-experiences', 'RecRateExperienceLookupController@index');
        Route::get('rate-experiences/list', array('as' => 'rate-experiences.list', 'uses' => 'RecRateExperienceLookupController@getList'));
        Route::get('rate-experiences/single/{id}', array('as' => 'rate-experiences.single', 'uses' => 'RecRateExperienceLookupController@getSingle'));
        Route::post('rate-experiences/store', array('as' => 'rate-experiences.store', 'uses' => 'RecRateExperienceLookupController@store'));
        Route::name('rate-experiences.destroy')->get('rate-experiences/{id}/destroy', 'RecRateExperienceLookupController@destroy');
        /* Rate Experiences - end */

        /* Commissionaires Understanding - start */
        Route::name('commissionaires-understanding')->get('commissionaires-understanding', 'RecCommissionairesUnderstandingLookupController@index');
        Route::get('commissionaires-understanding/list', array('as' => 'commissionaires-understanding.list', 'uses' => 'RecCommissionairesUnderstandingLookupController@getList'));
        Route::get('commissionaires-understanding/single/{id}', array('as' => 'commissionaires-understanding.single', 'uses' => 'RecCommissionairesUnderstandingLookupController@getSingle'));
        Route::post('commissionaires-understanding/store', array('as' => 'commissionaires-understanding.store', 'uses' => 'RecCommissionairesUnderstandingLookupController@store'));
        Route::get('commissionaires-understanding/destroy/{id}', array('as' => 'commissionaires-understanding.destroy', 'uses' => 'RecCommissionairesUnderstandingLookupController@destroy'));

        /* Commissionaires Understanding - end */

        /* Competency Matrix Rating- Start */
        Route::name('competency-matrix-rating')->get('competency-matrix-rating', 'RecCompetencyMatrixRatingLookupController@index');
        Route::name('competency-matrix-rating.list')->get('competency-matrix-rating/list', 'RecCompetencyMatrixRatingLookupController@getList');
        Route::name('competency-matrix-rating.single')->get('competency-matrix-rating/{id}/single', 'RecCompetencyMatrixRatingLookupController@get');
        Route::name('competency-matrix-rating.store')->post('competency-matrix-rating/store', 'RecCompetencyMatrixRatingLookupController@store');
        Route::name('competency-matrix-rating.destroy')->get('competency-matrix-rating/{id}/destroy', 'RecCompetencyMatrixRatingLookupController@destroy');
        /* Competency Matrix Rating- end */


        /* Process Tab- Start */
        Route::name('process-tab')->get('process-tab', 'RecProcessTabController@index');
        Route::name('process-tab.list')->get('process-tab/list', 'RecProcessTabController@getList');
        Route::name('process-tab.single')->get('process-tab/{id}/single', 'RecProcessTabController@getSingle');
        Route::name('process-tab.store')->post('process-tab/store', 'RecProcessTabController@store');
        Route::name('process-tab.destroy')->get('process-tab/{id}/destroy', 'RecProcessTabController@destroy');
        /*Process Tab- end */

        /* Competency-matrix category - start */
        Route::name('competency-matrix-category')->get('competency-matrix-category', 'RecCompetencyMatrixCategoryLookupController@index');
        Route::name('competency-matrix-category.list')->get('competency-matrix-category/list', 'RecCompetencyMatrixCategoryLookupController@getList');
        Route::name('competency-matrix-category.single')->get('competency-matrix-category/{id}/single', 'RecCompetencyMatrixCategoryLookupController@get');
        Route::name('competency-matrix-category.store')->post('competency-matrix-category/store', 'RecCompetencyMatrixCategoryLookupController@store');
        Route::name('competency-matrix-category.destroy')->get('competency-matrix-category/{id}/destroy', 'RecCompetencyMatrixCategoryLookupController@destroy');
        /* Competency-matrix category - End */

        /* Competency Matrix - start */
        Route::name('competency-matrix')->get('competency-matrix', 'RecCompetencyMatrixLookupController@index');
        Route::name('competency-matrix.list')->get('competency-matrix/list', 'RecCompetencyMatrixLookupController@getList');
        Route::name('competency-matrix.single')->get('competency-matrix/{id}/single', 'RecCompetencyMatrixLookupController@get');
        Route::name('competency-matrix.store')->post('competency-matrix/store', 'RecCompetencyMatrixLookupController@store');
        Route::name('competency-matrix.destroy')->get('competency-matrix/{id}/destroy', 'RecCompetencyMatrixLookupController@destroy');
        /* Competency Matrix - end */

        /* Job Criteria - start */
        Route::name('match-score-criteria')->get('match-score-criteria', 'RecMatchScoreCriteriaController@index');
        Route::name('match-score-criteria.list')->get('match-score-criteria/list', 'RecMatchScoreCriteriaController@getList');
        Route::name('match-score-criteria.single')->get('match-score-criteria/{id}/single', 'RecMatchScoreCriteriaController@get');
        Route::name('match-score-criteria.store')->post('match-score-criteria/store', 'RecMatchScoreCriteriaController@store');
        Route::name('match-score-criteria.destroy')->get('match-score-criteria/{id}/destroy', 'RecMatchScoreCriteriaController@destroy');
        /*  Job Criteria - end */

        // Route::name('match-score-criteria-mapping')->get('match-score-criteria', 'RecMatchScoreCriteriaRepositoryMappingController@index');
        // Route::name('match-score-criteria.list')->get('match-score-criteria/list', 'RecMatchScoreCriteriaRepositoryMappingController@getList');
        // Route::name('match-score-criteria.single')->get('match-score-criteria/{id}/single', 'RecMatchScoreCriteriaRepositoryMappingController@get');

        Route::name('match-score-criteria-mapping.list')->get('match-score-criteria-mapping/list/{id}', 'RecMatchScoreCriteriaMappingController@getCriteria');
        Route::name('match-score-criteria-mapping.store')->post('match-score-criteria-mapping/store', 'RecMatchScoreCriteriaMappingController@store');
        // Route::name('match-score-criteria.destroy')->get('match-score-criteria/{id}/destroy', 'RecMatchScoreCriteriaRepositoryMappingController@destroy');
        /*  Job Criteria - end */

        /* experience lookups- start */
        Route::name('criteria-lookups')->get('criteria-lookups', 'RecCriteriaLookupController@index');
        Route::get('criteria-lookups/list', array('as' => 'criteria-lookups.list', 'uses' => 'RecCriteriaLookupController@getList'));
        Route::get('criteria-lookups/single/{id}', array('as' => 'criteria-lookups.single', 'uses' => 'RecCriteriaLookupController@getSingle'));
        Route::post('criteria-lookups/store', array('as' => 'criteria-lookups.store', 'uses' => 'RecCriteriaLookupController@store'));
        Route::get('criteria-lookups/destroy/{id}', array('as' => 'criteria-lookups.destroy', 'uses' => 'RecCriteriaLookupController@destroy'));
        /* experience lookups - end */

        /* Security Clearance Lookup - start */
        Route::name('security-clearance')->get('security-clearance', 'RecSecurityClearanceLookupController@index');
        Route::get('security-clearance/list', array('as' => 'security-clearance.list', 'uses' => 'RecSecurityClearanceLookupController@getList'));
        Route::get('security-clearance/single/{id}', array('as' => 'security-clearance.single', 'uses' => 'RecSecurityClearanceLookupController@getSingle'));
        Route::post('security-clearance/store', array('as' => 'security-clearance.store', 'uses' => 'RecSecurityClearanceLookupController@store'));
        Route::get('security-clearance/destroy/{id}', array('as' => 'security-clearance.destroy', 'uses' => 'RecSecurityClearanceLookupController@destroy'));
        /* Security Clearance Lookup - end */

        /* Uniform Items - start */
        Route::name('uniform-items')->get('uniform-items', 'RecUniformItemController@index');
        Route::get('uniform-items/list', array('as' => 'uniform-items.list', 'uses' => 'RecUniformItemController@getList'));
        Route::get('uniform-items/add', array('as' => 'uniform-items.add', 'uses' => 'RecUniformItemController@addUniformItemSizeMeasurement'));
        Route::get('uniform-items/update/{id}', array('as' => 'uniform-items.update', 'uses' => 'RecUniformItemController@addUniformItemSizeMeasurement'));
        //Route::get('uniform-items/single/{id}', array('as' => 'uniform-items.single', 'uses' => 'RecUniformItemController@getSingle'));
        Route::post('uniform-items/store', array('as' => 'uniform-items.store', 'uses' => 'RecUniformItemController@store'));
        Route::get('uniform-items/destroy/{id}', array('as' => 'uniform-items.destroy', 'uses' => 'RecUniformItemController@destroy'));
        /* Uniform Items - end */

        /* Uniform Sizes - start */
        Route::name('uniform-sizes')->get('uniform-sizes', 'RecUniformSizeController@index');
        Route::get('uniform-sizes/list', array('as' => 'uniform-sizes.list', 'uses' => 'RecUniformSizeController@getList'));
        Route::get('uniform-sizes/single/{id}', array('as' => 'uniform-sizes.single', 'uses' => 'RecUniformSizeController@getSingle'));
        Route::post('uniform-sizes/store', array('as' => 'uniform-sizes.store', 'uses' => 'RecUniformSizeController@store'));
        Route::get('uniform-sizes/destroy/{id}', array('as' => 'uniform-sizes.destroy', 'uses' => 'RecUniformSizeController@destroy'));
        /* Uniform Sizes - end */

        /* Uniform measurement Points - start */
        Route::name('uniform-measurement-points')->get('uniform-measurement-points', 'RecUniformMeasurementPointController@index');
        Route::get('uniform-measurement-points/list', array('as' => 'uniform-measurement-points.list', 'uses' => 'RecUniformMeasurementPointController@getList'));
        Route::get('uniform-measurement-points/single/{id}', array('as' => 'uniform-measurement-points.single', 'uses' => 'RecUniformMeasurementPointController@getSingle'));
        Route::post('uniform-measurement-points/store', array('as' => 'uniform-measurement-points.store', 'uses' => 'RecUniformMeasurementPointController@store'));
        Route::get('uniform-measurement-points/destroy/{id}', array('as' => 'uniform-measurement-points.destroy', 'uses' => 'RecUniformMeasurementPointController@destroy'));
        /* Uniform measurement Points - end */


        /* Customer Uniform Kits - start */
        Route::name('customer-uniform-kits')->get('customer-uniform-kits', 'RecCustomerUniformKitController@index');
        Route::get('customer-uniform-kits/list', array('as' => 'customer-uniform-kits.list', 'uses' => 'RecCustomerUniformKitController@getList'));
        Route::get('customer-uniform-kits/add', array('as' => 'customer-uniform-kits.add', 'uses' => 'RecCustomerUniformKitController@addUniformKit'));
        Route::post('customer-uniform-kits/add', array('as' => 'customer-uniform-kits.add', 'uses' => 'RecCustomerUniformKitController@store'));
        Route::get('customer-uniform-kits/update/{id}', array('as' => 'customer-uniform-kits.update', 'uses' => 'RecCustomerUniformKitController@addUniformKit'));
        Route::get('customer-uniform-kits/destroy/{id}', array('as' => 'customer-uniform-kits.destroy', 'uses' => 'RecCustomerUniformKitController@destroy'));

        /* Customer Uniform Kits - end */

        /* Onboarding documents- Start */
        Route::name('onboarding-documents')->get('onboarding-documents', 'RecOnboardingDocumentsController@index');
        Route::name('onboarding-documents.list')->get('onboarding-documents/list', 'RecOnboardingDocumentsController@getList');
        Route::name('onboarding-documents.single')->get('onboarding-documents/{id}/single', 'RecOnboardingDocumentsController@getSingle');
        Route::name('onboarding-documents.store')->post('onboarding-documents/store', 'RecOnboardingDocumentsController@store');
        Route::name('onboarding-documents.destroy')->get('onboarding-documents/{id}/destroy', 'RecOnboardingDocumentsController@destroy');
        /*Onboarding documents- end */

        /* Document Allocation -Start */
        Route::name('document-allocation')->get('document-allocation', 'RecDocumentAllocationController@index');
        Route::name('document-allocation.get')->get('document-allocation/get/{cid?}', 'RecDocumentAllocationController@getList');
        Route::name('document-allocation.getCategoryDocument')->get('document-allocation/get/{custid}/{catid}', 'RecDocumentAllocationController@getCustCatList');
        Route::name('document-allocation.single')->get('document-allocation/{id}/single', 'RecDocumentAllocationController@getSingle');
        Route::name('document-allocation.store')->post('document-allocation/store', 'RecDocumentAllocationController@store');
        Route::name('document-allocation.destroy')->delete('document-allocation/{id}/destroy', 'RecDocumentAllocationController@destroy');
        /*Document Allocation - end */

        /* Job requisition reasons - start */

        Route::name('job-requisition-reason')->get('job-requisition-reason', 'RecJobRequisitionReasonLookupController@index');
        Route::get('job-requisition-reason/list', array('as' => 'job-requisition-reason.list', 'uses' => 'RecJobRequisitionReasonLookupController@getList'));
        Route::get('job-requisition-reason/single/{id}', array('as' => 'job-requisition-reason.single', 'uses' => 'RecJobRequisitionReasonLookupController@getSingle'));
        Route::post('job-requisition-reason/store', array('as' => 'job-requisition-reason.store', 'uses' => 'RecJobRequisitionReasonLookupController@store'));
        /* Job requisition reasons - end */

        /* Candidate Assignment Types Lookup - start */
        Route::name('candidate-assignment-type')->get('candidate-assignment-type', 'RecCandidateAssignmentTypeLookupController@index');
        Route::get('candidate-assignment-type/list', array('as' => 'candidate-assignment-type.list', 'uses' => 'RecCandidateAssignmentTypeLookupController@getList'));
        Route::get('candidate-assignment-type/single/{id}', array('as' => 'candidate-assignment-type.single', 'uses' => 'RecCandidateAssignmentTypeLookupController@getSingle'));
        Route::post('candidate-assignment-type/store', array('as' => 'candidate-assignment-type.store', 'uses' => 'RecCandidateAssignmentTypeLookupController@store'));
        Route::get('candidate-assignment-type/destroy/{id}', array('as' => 'candidate-assignment-type.destroy', 'uses' => 'RecCandidateAssignmentTypeLookupController@destroy'));
        /* Candidate Assignment Types Lookup - end */

        /* Timing Lookup - start */
        Route::name('training-timing')->get('training-timing', 'RecTrainingTimingLookupController@index');
        Route::get('training-timing/list', array('as' => 'training-timing.list', 'uses' => 'RecTrainingTimingLookupController@getList'));
        Route::get('training-timing/single/{id}', array('as' => 'training-timing.single', 'uses' => 'RecTrainingTimingLookupController@getSingle'));
        Route::post('training-timing/store', array('as' => 'training-timing.store', 'uses' => 'RecTrainingTimingLookupController@store'));
        Route::get('training-timing/destroy/{id}', array('as' => 'training-timing.destroy', 'uses' => 'RecTrainingTimingLookupController@destroy'));
        /* Timing Lookup - end */

        /* Training Lookup - start */
        Route::name('training')->get('training', 'RecTrainingLookupController@index');
        Route::get('training/list', array('as' => 'training.list', 'uses' => 'RecTrainingLookupController@getList'));
        Route::get('training/single/{id}', array('as' => 'training.single', 'uses' => 'RecTrainingLookupController@getSingle'));
        Route::post('training/store', array('as' => 'training.store', 'uses' => 'RecTrainingLookupController@store'));
        Route::get('training/destroy/{id}', array('as' => 'training.destroy', 'uses' => 'RecTrainingLookupController@destroy'));
        /* Training Lookup - end */

        /* Job Ticket Setting - start */
        Route::get('job-ticket-settings', array('as' => 'job-ticket-settings', 'uses' => 'RecJobTicketSettingsController@index'));
        Route::post('job-ticket-settings/store', array('as' => 'job-ticket-settings.store', 'uses' => 'RecJobTicketSettingsController@store'));
        /* Job Ticket Setting - end */

        /* Score Criteria - Start */
        Route::get('score-criteria', array('as' => 'score-criteria', 'uses' => 'RecMatchScoreCriteriaController@getScoreCriteria'));
        Route::get('score-criteria/list', array('as' => 'score-criteria.list', 'uses' => 'RecMatchScoreCriteriaController@getScoreCriteriaList'));
        /* Score Criteria - End */

        /* Candidate feedback - Start */
        Route::name('candidate-feedback-lookup')->get('candidate-feedback-lookup', 'RecFeedbackLookupController@index');
        Route::get('candidate-feedback-lookup/list', array('as' => 'candidate-feedback-lookup.list', 'uses' => 'RecFeedbackLookupController@getList'));
        Route::get('candidate-feedback-lookup/single/{id}', array('as' => 'candidate-feedback-lookup.single', 'uses' => 'RecFeedbackLookupController@getSingle'));
        Route::post('candidate-feedback-lookup/store', array('as' => 'candidate-feedback-lookup.store', 'uses' => 'RecFeedbackLookupController@store'));
        Route::get('candidate-feedback-lookup/destroy/{id}', array('as' => 'candidate-feedback-lookup.destroy', 'uses' => 'RecFeedbackLookupController@destroy'));
        /* Candidate Feedback - End */

        /* Licence threshold - start */
        Route::name('licence-threshold')->get('licence-threshold', 'RecLicenceThresholdController@index');
        Route::post('licence-threshold/store', array('as' => 'licence-threshold.store', 'uses' => 'RecLicenceThresholdController@store'));
        /* Licence threshold - end */
    });
});

