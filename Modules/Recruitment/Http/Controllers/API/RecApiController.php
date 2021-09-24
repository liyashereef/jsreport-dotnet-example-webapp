<?php

namespace Modules\Recruitment\Http\Controllers\API;

use App\Helpers\S3HelperService;
use App\Http\Controllers\Controller;
use App\Services\HelperService;
use App\Services\LocationService;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Recruitment\Models\RecCandidateScreeningQuestionLookups;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\DivisionLookup;
use Modules\Admin\Models\JobPostFindingLookup;
use Modules\Admin\Models\Languages;
use Modules\Admin\Models\PositionLookup;
use Modules\Admin\Models\SecurityProviderLookup;
use Modules\Admin\Models\SmartPhoneType;
use Modules\Admin\Repositories\UserRepository;
use Modules\LearningAndTraining\Models\TrainingUser;
use Modules\LearningAndTraining\Repositories\CourseContentRepository;
use Modules\LearningAndTraining\Repositories\TestUserResultRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseRepository;
use Modules\LearningAndTraining\Repositories\TrainingCourseUserRatingRepository;
use Modules\LearningAndTraining\Repositories\TrainingTestQuestionsRepository;
use Modules\LearningAndTraining\Repositories\TrainingTestRepository;
use Modules\LearningAndTraining\Repositories\TrainingTestSettingsRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserContentRepository;
use Modules\LearningAndTraining\Repositories\TrainingUserCourseAllocationRepository;
use Modules\Recruitment\Http\Controllers\RecJobApplicationController;
use Modules\Recruitment\Models\RecBrandAwareness;
use Modules\Recruitment\Models\RecCandidate;
use Modules\Recruitment\Models\RecCandidateAttachment;
use Modules\Recruitment\Models\RecCandidateDocuments;
use Modules\Recruitment\Models\RecCandidateJobDetails;
use Modules\Recruitment\Models\RecCandidateScreeningCompetencyMatrix;
use Modules\Recruitment\Models\RecCandidateScreeningPersonalityTestQuestion;
use Modules\Recruitment\Models\RecCandidateScreeningQuestion;
use Modules\Recruitment\Models\RecCandidateTracking;
use Modules\Recruitment\Models\RecCandidateUniformSize;
use Modules\Recruitment\Models\RecCommissionairesUnderstandingLookup;
use Modules\Recruitment\Models\RecCustomerUniformKit;
use Modules\Recruitment\Models\RecJob;
use Modules\Recruitment\Models\RecJobDocumentAllocation;
use Modules\Recruitment\Models\RecRateExperienceLookups;
use Modules\Recruitment\Models\RecSecurityAwareness;
use Modules\Recruitment\Models\RecSecurityGuardLicenceThresholds;
use Modules\Recruitment\Models\RecUniformMeasurementPoint;
use Modules\Recruitment\Models\RecUseOfForceLookups;
use Modules\Recruitment\Repositories\RecCandidateAttachmentRepository;
use Modules\Recruitment\Repositories\RecCandidateJobDetailsRepository;
use Modules\Recruitment\Repositories\RecCandidateRepository;
use Modules\Recruitment\Repositories\RecCandidateScreeningCompetencyMatrixRepository;
use Modules\Recruitment\Repositories\RecCandidateScreeningPersonalityInventoryRepository;
use Modules\Recruitment\Repositories\RecCandidateScreeningPersonalityScoreRepository;
use Modules\Recruitment\Repositories\RecCandidateTrackingRepository;
use Modules\Recruitment\Repositories\RecCompetencyMatrixLookupRepository;
use Modules\Recruitment\Repositories\RecCompetencyMatrixRatingLookupRepository;
use Modules\Recruitment\Repositories\RecCustomerUniformKitRepository;
use Modules\Recruitment\Repositories\RecJobDocumentAllocationRepository;
use Modules\Recruitment\Repositories\RecProcessTabRepository;
use Modules\LearningAndTraining\Models\TrainingTeamCourseAllocation;

class RecApiController extends Controller
{

    public $successStatus = 200;


    /**
     * The RecProcessTabRepository instance.
     *
     * @var \App\Repositories\RecProcessTabRepository
     */
    protected $recProcessTabRepository;
    protected $recCandidateRepository;
    protected $recCandidateJobDetailsRepository;
    protected $userRepository;
    protected $s3HelperService;

    private $competency_matrix_rating_lookup_repository;
    private $competency_matrix_lookup_repository;
    private $locationService;


    /**
     * Create a new Repository instance.
     *
     * @param RecProcessTabRepository $recProcessTabRepository
     * @param RecCandidateRepository $recCandidateRepository
     * @param RecCompetencyMatrixLookupRepository $competencyMatrixLookupRepository
     * @param RecCompetencyMatrixRatingLookupRepository $competencyMatrixRatingLookupRepository
     * @param RecCandidateScreeningPersonalityScoreRepository $personality_score_repository
     * @param RecCandidateScreeningPersonalityInventoryRepository $personality_inventory_repository
     * @param RecCandidateJobDetailsRepository $recCandidateJobDetailsRepository
     * @param RecCustomerUniformKitRepository $recCustomerUniformKitRepository
     * @param RecJobDocumentAllocationRepository $recJobDocumentAllocationRepository
     * @param RecJobApplicationController $recJobApplicationController
     * @param RecCandidateTrackingRepository $recCandidateTrackingRepository
     * @param RecCandidateScreeningCompetencyMatrixRepository $candidateScreeningCompetencyMatrixRepository
     * @param UserRepository $userRepository
     * @param RecCandidateAttachmentRepository $recCandidateAttachmentRepository
     * @param TrainingCourseRepository $trainingCourseRepository
     * @param CourseContentRepository $courseContentRepository
     * @param TrainingUserCourseAllocationRepository $trainingUserCourseAllocationRepository
     * @param TrainingCourseUserRatingRepository $trainingCourseUserRatingRepository
     * @param TrainingTestSettingsRepository $trainingTestSettingsRepository
     * @param TestUserResultRepository $testUserResultRepository
     * @param TrainingTestQuestionsRepository $trainingTestQuestionsRepository
     * @param TrainingTestRepository $trainingTestRepository
     * @param TrainingUserContentRepository $trainingUserContentRepository
     * @param S3HelperService $s3HelperService
     */
    public function __construct(
        RecProcessTabRepository $recProcessTabRepository,
        RecCandidateRepository $recCandidateRepository,
        RecCompetencyMatrixLookupRepository $competencyMatrixLookupRepository,
        RecCompetencyMatrixRatingLookupRepository $competencyMatrixRatingLookupRepository,
        RecCandidateScreeningPersonalityScoreRepository $personality_score_repository,
        RecCandidateScreeningPersonalityInventoryRepository $personality_inventory_repository,
        RecCandidateJobDetailsRepository $recCandidateJobDetailsRepository,
        RecCustomerUniformKitRepository $recCustomerUniformKitRepository,
        RecJobDocumentAllocationRepository $recJobDocumentAllocationRepository,
        RecJobApplicationController $recJobApplicationController,
        RecCandidateTrackingRepository $recCandidateTrackingRepository,
        RecCandidateScreeningCompetencyMatrixRepository $candidateScreeningCompetencyMatrixRepository,
        UserRepository $userRepository,
        RecCandidateAttachmentRepository $recCandidateAttachmentRepository,
        TrainingCourseRepository $trainingCourseRepository,
        CourseContentRepository $courseContentRepository,
        TrainingUserCourseAllocationRepository $trainingUserCourseAllocationRepository,
        TrainingCourseUserRatingRepository $trainingCourseUserRatingRepository,
        TrainingTestSettingsRepository $trainingTestSettingsRepository,
        TestUserResultRepository $testUserResultRepository,
        TrainingTestQuestionsRepository $trainingTestQuestionsRepository,
        TrainingTestRepository $trainingTestRepository,
        TrainingUserContentRepository $trainingUserContentRepository,
        S3HelperService $s3HelperService,
        LocationService $locationService
    ) {
        $this->recProcessTabRepository = $recProcessTabRepository;
        $this->recCandidateRepository = $recCandidateRepository;
        $this->competency_matrix_lookup_repository = $competencyMatrixLookupRepository;
        $this->competency_matrix_rating_lookup_repository = $competencyMatrixRatingLookupRepository;
        $this->personality_score_repository = $personality_score_repository;
        $this->personality_inventory_repository = $personality_inventory_repository;
        $this->recCandidateJobDetailsRepository = $recCandidateJobDetailsRepository;
        $this->helper_service = new HelperService();
        $this->recCustomerUniformKitRepository = $recCustomerUniformKitRepository;
        $this->recJobDocumentAllocationRepository = $recJobDocumentAllocationRepository;
        $this->recJobApplicationController = $recJobApplicationController;
        $this->recCandidateTrackingRepository = $recCandidateTrackingRepository;
        $this->candidate_screening_competency_matrix_repository = $candidateScreeningCompetencyMatrixRepository;
        $this->userRepository = $userRepository;
        $this->recCandidateAttachmentRepository = $recCandidateAttachmentRepository;
        $this->user_courses = $trainingUserCourseAllocationRepository;
        $this->trainingCourseRepository = $trainingCourseRepository;
        $this->courseContentRepository = $courseContentRepository;
        $this->trainingCourseUserRatingRepository = $trainingCourseUserRatingRepository;
        $this->trainingTestSettingsRepository = $trainingTestSettingsRepository;
        $this->testUserResultRepository = $testUserResultRepository;
        $this->trainingTestQuestionsRepository = $trainingTestQuestionsRepository;
        $this->trainingTestRepository = $trainingTestRepository;
        $this->trainingUserContentRepository = $trainingUserContentRepository;
        $this->s3HelperService = $s3HelperService;
        $this->locationService = $locationService;
    }

    /**
     * List all tabs
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProcessTab(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $tabList = $this->recProcessTabRepository->getAll();
            $content['tabs'] = $tabList;
            $content['success'] = true;
            $content['message'] = 'ok';
            $content['code'] = $this->successStatus;
        } catch (\Exception $e) {
            $content['success'] = false;
            $content['message'] = $e->getMessage();
            $content['code'] = 406;
        }
        return response()->json(['content' => $content], $content['code']);
    }

    /**
     * Display details of single candidate
     *
     * @param $id
     * @return json
     */
    public function getSingleCandidateCredential()
    {
        $candidateId = Auth::user()->id;
        return response()->json($this->recCandidateRepository->getCandidateCredential($candidateId));
    }

    /**
     * Update candidate credentials
     * @param Request $request
     * @return Response
     */
    public function updateCandidateCredential(Request $request)
    {
        try {
            $this->recCandidateRepository->updateCandidateCredentials($request->all());
            $msg = 'Done';
            $status = 200;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $status = 400;
        }
        return response()->json(['message' => $msg], $status);
    }


    /**
     * Candidate Login
     * @param Request $request
     * @return Response
     */
    /*   public function login(Request $request)
    {
        try {

            if(auth()->guard('rec_candidate')->attempt(['username' => request('username'), 'password' => request('password')])){

                config(['auth.guards.api.provider' => 'rec_candidate']);

                $admin = RecCandidate::select('*')->find(auth()->guard('rec_candidate')->user()->id);
                $success =  $admin;
                $success['token'] =  $admin->createToken('MyApp',['rec_candidate'])->accessToken;
                $status = 200;
                $msg ='Success';
            }else{
                $status = 400;
                $msg ='Invalid email or password.';
            }
        } catch (\Exception $e) {
            $status = 406;
            $msg = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
        }
        return response()->json(['message' =>$msg, 'status' =>$status ]);
    }*/


    /**
     * Get Candidate Profile
     */
    public function candidateProfile()
    {
        $candidateProfile = $this->recCandidateRepository->getCandidateProfile();

        return response()->json(["profile" => $candidateProfile['profile'], "lookups" => $candidateProfile['lookups']]);
    }

    /**
     * Get Screening Question
     *
     */
    public function getScreeningQuestion()
    {
        $process = [];
        $candidate = \Auth::user();
        //$count = RecCandidateScreeningQuestion::where(['candidate_id' => $candidate->id])->count();
        $count = RecCandidateTracking::where(['candidate_id' => $candidate->id])->where('process_lookups_id', 4)->count();
        $status = RecCandidate::select('is_completed')->where('id', $candidate->id)->first();
        if ($status['is_completed'] == 1) {
            $lookups['application_completed'] = 1;
        } else {
            $lookups['application_completed'] = 0;
        }

        if ($count > 0) {
            $lookups['casestudy_completed'] = 1;
            $lookups['screening_questions_answers'] = RecCandidateScreeningQuestion::where('candidate_id', $candidate->id)
                ->pluck('answer', 'question_id');
        }
        $screeningQuestion = RecCandidateScreeningQuestionLookups::get();
        $collection = collect($screeningQuestion);
        $lookups['screening_questions'] = $collection->groupBy('category');
        $process = $this->getProcessArr('screening_questions');
        return response()->json(['process' => $process, 'content' => $lookups]);
    }

    /**
     * Get candidate personality questions
     */
    public function getPersonalityQuestions()
    {
        $candidate = \Auth::user();
        $check_candidate_score = $this->personality_score_repository->checkScore($candidate->id);
        if ($check_candidate_score > 0) {
            $lookups['personality_questions'] = "COMPLETED";
        } else {
            $lookups['personality_questions'] = RecCandidateScreeningPersonalityTestQuestion::with(['options'])->get();
        }
        $process = $this->getProcessArr('personality');
        return response()->json(['process' => $process, 'content' => $lookups]);
    }

    /**
     * Get candidate competency matrix
     */
    public function getCompetencyMatrix()
    {
        $candidate = \Auth::user();
        $count = RecCandidateTracking::where(['candidate_id' => $candidate->id])->where('process_lookups_id', 6)->count();
        $status = RecCandidate::select('is_completed')->where('id', $candidate->id)->first();
        if ($status['is_completed'] == 1) {
            $lookups['application_completed'] = 1;
        } else {
            $lookups['application_completed'] = 0;
        }
        if ($count > 0) {
            $lookups['competency_completed'] = 1;
            $val = RecCandidateScreeningCompetencyMatrix::where('candidate_id', '=', $candidate->id)->get();
            $candidate_rating = [];
            foreach ($val as $key => $value) {
                $candidate_rating[$key + 1] = $value->competency_matrix_rating_lookup_id;
            }
            $lookups['candidate_rating'] = $candidate_rating;
        }
        $lookups['competency_matrix'] = $this->competency_matrix_lookup_repository->getCompetency();
        $allratings = [];
        $ratings = $this->competency_matrix_rating_lookup_repository->getAll();
        foreach ($ratings as $key => $value) {
            $allratings[$key]['id'] = $value->id;
            $allratings[$key]['option'] = $value->rating;
        }
        $lookups['competency_rating'] = $allratings;

        $process = $this->getProcessArr('competency');
        return response()->json(['process' => $process, 'content' => $lookups]);
    }

    /**
     * Get candidate attachments
     */
    public function getAttachments(Request $request)
    {
        $process['completed'] = false;
        $candidate = \Auth::user();
        $required_attachment = array();
        $uploadedItems = array();
        $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 3)->where('candidate_id', '=', $candidate->id)->first();
        $request->jobId = $job_details->job_id; // add validation if no job id
        $job = RecJob::find($job_details->job_id);
        $lookups['onboarding_status'] = $this->getOnboardingStatus($job_details->job_id);
        $required_attachment = json_decode($job->required_attachments);
        $process = $this->getProcessArr('attachments');
        if ($required_attachment != null) {
            $mandatory_doc = array_unique($required_attachment);
            $uploadedItems = RecCandidateAttachment::where('candidate_id', $candidate->id)->pluck('attachment_id')->toArray();
            if (count(array_intersect(array_unique($uploadedItems), $mandatory_doc)) == count($mandatory_doc)) {
                $process['completed'] = true;
            }
        } else {
            $process['completed'] = true;
        }
        $lookups['attachments'] = $this->recCandidateAttachmentRepository->getAttachmentApi($request);
        return response()->json(['process' => $process, 'content' => $lookups]);
    }

    /**
     * Get documents according to process tab and job id
     * @param Request request
     * @param Reponse json
     */
    public function getDocuments(Request $request)
    {
        $candidate = \Auth::user();
        $process['completed'] = false;
        $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 3)->where('candidate_id', '=', $candidate->id)->first();
        $request->jobId = $job_details->job_id; // add validation if no job id
        $request->candidateId = $candidate->id;
        $tab_id = $request->processTabId;
        $mandatory = RecJobDocumentAllocation::where('job_id', $job_details->job_id)->where('process_tab_id', $tab_id)->where('is_mandatory', 1)->pluck('id')->toArray();
        $uploadedItems = RecCandidateDocuments::where('candidate_id', $candidate->id)->pluck('rec_job_document_allocation_id')->toArray();
        $lookups['onboarding_status'] = $this->getOnboardingStatus($job_details->job_id);
        $mandatory_doc = array_unique($mandatory);
        $lookups['documents'] = $this->recJobDocumentAllocationRepository->getDocumentApi($request);
        $process = $this->getProcessArr($request->processTabId);
        if (count(array_intersect(array_unique($uploadedItems), $mandatory_doc)) == count($mandatory_doc)) {
            $process['completed'] = true;
        }
//        $details = $this->recCandidateTrackingRepository->getProcessStep($candidate->id);
//        $process['max_process'] = $details['next_step'];
//        $process['current_tab'] = $details['current_tab'];
//        $details = $this->recProcessTabRepository->getInstructionById($request->processTabId);
//        $process['instructions'] = $details->instructions;
        return response()->json(['process' => $process, 'content' => $lookups]);
    }

    public function getOnboardingStatus($job_id)
    {
        $job = RecJob::where('id', $job_id)
            ->select('id', 'unique_key', 'open_position_id', 'customer_id', 'required_job_start_date')
            ->with(['positionBeeingHired', 'customer'])
            ->get();

        $onBoardingStatus = [];
        if (null !== $job) {
            foreach ($job as $value) {
                $onBoardingStatus['job_id'] = $value->unique_key;
                $onBoardingStatus['position_being_hired'] = $value->positionBeeingHired->position;
                $onBoardingStatus['customer_id'] = $value->customer->project_number;
                $onBoardingStatus['client_name'] = $value->customer->client_name;
                $customer = Customer::find($value->customer_id);
                $onBoardingStatus['onboarding_deadline'] = Carbon::parse($value->required_job_start_date)
                    ->subDays($customer->rec_onboarding_threshold_days)->format('Y-m-d');
                $onBoardingStatus['job_start_date'] = $value->required_job_start_date;
            }
        }
        return $onBoardingStatus;
    }

    public function destroyCandidateDocument(Request $request)
    {
        $candidate = \Auth::user();
        RecCandidateDocuments::where('candidate_id', $candidate->id)->where('rec_job_document_allocation_id', $request->id)->delete();
        return response()->json(array('status' => true));
    }


    public function destroyCandidateAttachment(Request $request)
    {
        $candidateId = Auth::user()->id;
        $result = $this->recCandidateAttachmentRepository->removeAttachment($request->id, $candidateId);
        return response()->json(array('status' => true));
    }


    public function getUniform(Request $request)
    {
        $candidate = \Auth::user();
        $candidateDetails = RecCandidate::find($candidate->id);
        $lookups['candidate_address'] = $candidateDetails->full_address;
        $lookups['gender'] = $candidateDetails->gender;

        $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 3)->where('candidate_id', '=', $candidate->id)->first();
        $job = RecJob::find($job_details->job_id);
        $lookups['uniformKit'] = RecCustomerUniformKit::where('customer_id', $job->customer_id)
            ->select('id', 'kit_name')
            ->first();
        if ($lookups['uniformKit']) {
            $lookups['uniformKit'] = $lookups['uniformKit']->toArray();
        }
        if ($candidateDetails->gender == 1) { //Male
            $lookups['measuringPoints'] = RecUniformMeasurementPoint::select('id', 'name')->where('name', '!=', 'Hip')
                ->get()->toArray();
        } else {
            $lookups['measuringPoints'] = RecUniformMeasurementPoint::select('id', 'name')
                ->get()->toArray();
        }

        $lookups['already_exists'] = RecCandidateUniformSize::where('candidate_id', $candidate->id)->where('customer_id', $job->customer_id)->count();
        $process = $this->getProcessArr('uniform');
        return response()->json(['process' => $process, 'content' => $lookups]);
    }


    public function acceptTermsAndConditions(Request $request)
    {
        try {
            $this->recCandidateRepository->acceptTermsAndConditions($request->all());
            $msg = 'Done';
            $status = 200;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $status = 400;
        }
        return response()->json(['message' => $msg], $status);
    }


    public function storeProfilePic(Request $request)
    {
        try {
            DB::beginTransaction();
            $candidate = \Auth::user();

            $candidateImageName = $this->userRepository->uploadProfileImage($request, $candidate->id, 'candidate_profile');
            if ($candidateImageName != null) {
                $insertCandidate['profile_image'] = $candidateImageName;
            }
            RecCandidate::updateOrCreate(['id' => $candidate->id], $insertCandidate);
            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e);
        }
    }

    // answer: { "3" : "MY OBSERVATION IS THAT IN MY LAST SITE DUE TO TENSIONS RIDING HIGH TO COMPLETE THE PROJECT ALL OCCUPANTS FROM MANAGEMENT TO WORKER WAS UNDER SEVERE STRESS AND IT CAUSED ME TO BE PROACTIVE CALM AND OBJECTIVE .","4": "WHEN HEATED SITUATIONS OCCUR NOBODY IDEAS ARE EXCEPTED WHICH IS A REALITY - WHEN MY CO-WORKER ."}
    // _sc:{ "3": "Good",  "4" : "Fair"}
    // candidate_id:1

    public function storeScreeningQuestions(Request $request)
    {
        try {
            DB::beginTransaction();
            $candidate = \Auth::user();
            RecCandidateScreeningQuestion::where(['candidate_id' => $candidate->id])->delete();
            $answers = $request->get('answer');
            foreach ($answers[0] as $id => $question) {
                if ($question != null) {
                    $candidatescreening = new RecCandidateScreeningQuestion;
                    $candidatescreening->candidate_id = $candidate->id;
                    $candidatescreening->question_id = $id;
                    $candidatescreening->answer = $question;
                    $candidatescreening->score = isset($scores[$id]) ? $scores[$id] : null;
                    $candidatescreening->save();
                }
            }
            $this->recCandidateTrackingRepository->saveTracking($candidate->id, "screening_questions");
            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e);
        }
    }

    //     candidate_id:1
    // arr:[{ "question_id":"1","question_option_id" : "1" }]

    public function storePersonality(Request $request)
    {
        try {
            $candidate = \Auth::user();
            $check_candidate_score = $this->personality_score_repository->checkScore($candidate->id);
            if ($check_candidate_score > 0) {
                return response()->json(array('success' => true, 'message' => 'Already calculated score'));
            }
            DB::beginTransaction();
            $arr = $request->get('answers');
            foreach ($arr as $question) {
                $personality_test['candidate_id'] = $candidate->id;
                $personality_test['question_id'] = $question['question_id'];
                $personality_test['question_option_id'] = $question['question_option_id'];
                $this->personality_inventory_repository->store($personality_test);
            }
            $score_result = $this->personality_score_repository->calculateScore($candidate->id);
            $this->recCandidateTrackingRepository->saveTracking($candidate->id, "personality");

            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e);
        }
    }

    public function getJobList(Request $request)
    {
        $candidate = \Auth::user();
        $jobList = array();
        $process = $this->getProcessArr('apply_for_jobs');

        try {
            $jobList = $this->recCandidateJobDetailsRepository->getJobListForCandidate($candidate->id);
            $msg = 'Done';
            $status = 200;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $status = 400;
        }
        return response()->json(['process' => $process, 'content' => $jobList]);
    }

    public function updateJobPreference(Request $request)
    {
        try {
            DB::beginTransaction();
            $candidate = \Auth::user();
            $appliedjob = $request->all();
            foreach ($appliedjob as $eachjob) {
                $data = [
                    'id' => $eachjob['id'],
                    'rec_preference' => $eachjob['preference'],
                ];
                RecCandidateJobDetails::updateOrCreate(array('id' => $data['id']), $data);
            }
            $this->recCandidateTrackingRepository->saveTracking($candidate->id, "apply_for_jobs");

            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e);
        }
    }

    public function getJobAppliedList(Request $request)
    {
        $jobAppliedList = array();
        $candidateId = Auth::user()->id;
        $process = $this->getProcessArr('apply_for_jobs');
        try {
            $jobAppliedList = $this->recCandidateJobDetailsRepository->getJobAppliedListCandidate($candidateId);
            $msg = 'Done';
            $status = 200;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $status = 400;
        }
        return response()->json(['process' => $process, 'content' => $jobAppliedList]);
    }

    public function storeUniformMeasurement(Request $request)
    {
        try {
            $candidateId = Auth::user()->id;
            $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 3)->where('candidate_id', '=', $candidateId)->first();

            // add validation if no job id
            $UniformDetails = $this->recCustomerUniformKitRepository->saveUniformDetails($request->all(), $candidateId, $job_details->job_id);
            $tracking = $this->recCandidateTrackingRepository->saveTracking($candidateId, "uniform_measurement_completed", false, $job_details->job_id);

            $msg = 'Done';
            $status = 200;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $status = 400;
        }
        return response()->json(['message' => $msg, 'uniform' => $UniformDetails], $status);
    }

    /**
     * Get Screening Question
     *
     */
    public function getProfile()
    {
        $process = [];
        $lookups['brandawareness'] = $this->prepareArray(RecBrandAwareness::orderBy('order_sequence')->pluck('answer', 'id')->toArray());
        $lookups['securityawareness'] = $this->prepareArray(RecSecurityAwareness::orderBy('order_sequence')->pluck('answer', 'id')->toArray());
        $lookups['commissionaires_understandings'] = $this->prepareArray(RecCommissionairesUnderstandingLookup::orderBy('order_sequence')->pluck('commissionaires_understandings', 'id')->toArray());
        $lookups['positions_lookups'] = $this->prepareArray(PositionLookup::orderBy('position', 'asc')->pluck('position', 'id')->toArray() + array(0 => 'Other'));
        $lookups['security_provider'] = $this->prepareArray(SecurityProviderLookup::orderBy('security_provider', 'asc')->pluck('security_provider', 'id')->toArray());
        $lookups['securityproviderArray'] = SecurityProviderLookup::orderBy('security_provider', 'asc')->pluck('security_provider', 'id')->toArray();
        $lookups['experience_ratings'] = $this->prepareArray(RecRateExperienceLookups::orderBy('score', 'desc')->pluck('experience_ratings', 'id')->toArray());
        $lookups['smart_phone_types'] = $this->prepareArray(SmartPhoneType::pluck('type', 'id')->toArray());
        $lookups['division'] = $this->prepareArray(DivisionLookup::pluck('division_name', 'id')->toArray());
        $lookups['languages'] = $this->prepareArray(Languages::pluck('language', 'id')->toArray());
        $lookups['job_post_finding_collection'] = $this->prepareArray(JobPostFindingLookup::orderby('id', 'asc')->get()->pluck('job_post_finding', 'id')->toArray());
        $lookups['use_of_forcelookup'] = $this->prepareArray(RecUseOfForceLookups::orderby('order_sequence', 'asc')->get()->pluck('use_of_force', 'id')->toArray());
        $process = $this->getProcessArr('profile_completed');
        $formFields = RecCandidate::with('referalAvailibility', 'awareness', 'guardingExperience', 'wageExpectation', 'availability', 'securityclearance', 'securityproximity', 'experience', 'miscellaneous', 'languages', 'skills', 'employment_history', 'references', 'educations', 'comissionaires_understanding', 'other_languages', 'force')->find(\Auth::user()->id);
        $threshold = RecSecurityGuardLicenceThresholds::first()->threshold;
        $lookups['ots_threshold'] = $threshold;
        return response()->json(['process' => $process, 'content' => $lookups, 'formFields' => $formFields]);
    }

    public function getProcessArr($currentProcess): array
    {
        $candidate = \Auth::user();
        $details = $this->recCandidateTrackingRepository->getProcessStep($candidate->id);
        $process['max_tab'] = $details['next_tab'];
        $process['max_route'] = $details['next_route'];
        $process['max_process'] = $details['next_step'];
        $process['current_tab'] = $details['current_tab'];
        if (is_numeric($currentProcess)) {
            $process['current_process'] = $this->recProcessTabRepository->getProcessById($currentProcess);
        } else {
            $process['current_process'] = $currentProcess;
        }
        $details = $this->recProcessTabRepository->getInstruction($process['current_process']);
        $process['instructions'] = $details->instructions;
        return $process;
    }

    public function prepareArray($data)
    {
        $arr = [];
        $result = [];
        foreach ($data as $key => $value) {
            $arr['value'] = $key;
            $arr['text'] = $value;
            $result[] = $arr;
        }
        return $result;
    }

    public function storeProfile(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $candidate_id = \Auth::user()->id;
            $data = $request->all()['data'];
            $this->recJobApplicationController->storeJob($data, $candidate_id);
            //RecCandidate::where(['candidate_id' => $candidate->id])->delete();
            // if (null !== $request->get('prev_address')) {
            //     $this->storeAddress($request, $candidate_id);
            // }
            $this->recJobApplicationController->storeCandidate($data, $candidate_id);
            $this->recJobApplicationController->storeCandidateReferalAvailability($data, $candidate_id);
            $this->recJobApplicationController->storeCandidateCommissionairesUnderstanding($data, $candidate_id);
            $this->recJobApplicationController->storeCandidateForceCertification($data, $candidate_id);
            $this->recJobApplicationController->storeSecurityExperience($data, $candidate_id);
            $this->recJobApplicationController->storeWageExpectation($data, $candidate_id);
            $this->recJobApplicationController->storeAvailability($data, $candidate_id);
            $this->recJobApplicationController->storeSecurityClearance($data, $candidate_id);
            $this->recJobApplicationController->storeSecurityProximity($data, $candidate_id);
            $this->recJobApplicationController->saveEmployementHistory($data, $candidate_id);
            $this->recJobApplicationController->storeReference($data, $candidate_id);
            $this->recJobApplicationController->storeEducation($data, $candidate_id);
            $this->recJobApplicationController->storeExperiences($data, $candidate_id);
            $this->recJobApplicationController->storeMiscellaneous($data, $candidate_id);
            $this->recJobApplicationController->storeLanguages($data, $candidate_id);
            $this->recJobApplicationController->storeSkills($data, $candidate_id);
            $this->recJobApplicationController->storeOtherlanguage($data, $candidate_id);
            $this->recCandidateTrackingRepository->saveTracking($candidate_id, "profile_completed");

            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e);
        }
    }


    /**
     * Update candidate credentials
     * @param Request $request arr:[{ "competency_lookup_id":"1","rating" : "1" }]
     * @return Response
     */

    public function storeCompetency(Request $request)
    {
        try {
            DB::beginTransaction();
            $candidate_id = \Auth::user()->id;
            $this->candidate_screening_competency_matrix_repository->deleteAll($candidate_id);
            $ratings = $request->get('ratings');
            foreach ($ratings as $id => $eachrating) {
                if ($eachrating != 0) {
                    $competency_matrix['candidate_id'] = $candidate_id;
                    $competency_matrix['competency_matrix_lookup_id'] = $id;
                    $competency_matrix['competency_matrix_rating_lookup_id'] = $eachrating;
                    $this->candidate_screening_competency_matrix_repository->store($competency_matrix);
                }
            }
            $this->recCandidateTrackingRepository->saveTracking($candidate_id, "competency");
            //    $data['id']=$candidate_id;
            //    $candidate=$this->recCandidateRepository->updateCandidateCredentials($data);
            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e);
        }
    }

    /**
     * Store to Candidate Documents
     * @param Request
     * @return Response
     */
    public function storeCandidateDocument(Request $request)
    {
        try {
            DB::beginTransaction();
            $candidateId = Auth::user()->id;
            $document = $request->get('docdata');
            if (($document['filename'] != null) && ($document['id'] != null)) {
                $result = RecCandidateDocuments::create([
                    'candidate_id' => $candidateId,
                    'rec_job_document_allocation_id' => $document['id'],
                    'file_name' => $document['filename']
                ]);
               /* $details = $this->recJobDocumentAllocationRepository->getJobDocumentDetails($document['id']);
                $job_details = RecCandidateJobDetails::select('job_id')->where('status', 3)->where('candidate_id', $candidateId)->first();
                if ($details->process_tab_id == 6) {
                    $mandatory = RecJobDocumentAllocation::where('job_id', $job_details->job_id)->where('process_tab_id', 6)->where('is_mandatory', 1)->pluck('id')->toArray();
                    $uploadedItems = RecCandidateDocuments::where('candidate_id', $candidateId)->pluck('rec_job_document_allocation_id')->toArray();
                    $mandatory_doc = array_unique($mandatory);
                    if (count(array_intersect(array_unique($uploadedItems), $mandatory_doc)) == count($mandatory_doc)) {
                        $this->recCandidateTrackingRepository->saveTracking($candidateId, "enrollment_completed", false, $job_details->job_id);
                    }
                } elseif ($details->process_tab_id == 7) {
                    $mandatory = RecJobDocumentAllocation::where('job_id', $job_details->job_id)->where('process_tab_id', 7)->where('is_mandatory', 1)->pluck('id')->toArray();
                    $uploadedItems = RecCandidateDocuments::where('candidate_id', $candidateId)->pluck('rec_job_document_allocation_id')->toArray();
                    $mandatory_doc = array_unique($mandatory);
                    if (count(array_intersect(array_unique($uploadedItems), $mandatory_doc)) == count($mandatory_doc)) {
                        $this->recCandidateTrackingRepository->saveTracking($candidateId, "security_clearance_completed", false, $job_details->job_id);
                    }
                } elseif ($details->process_tab_id == 8) {
                    $mandatory = RecJobDocumentAllocation::where('job_id', $job_details->job_id)->where('process_tab_id', 8)->where('is_mandatory', 1)->pluck('id')->toArray();
                    $uploadedItems = RecCandidateDocuments::where('candidate_id', $candidateId)->pluck('rec_job_document_allocation_id')->toArray();
                    $mandatory_doc = array_unique($mandatory);
                    if (count(array_intersect(array_unique($uploadedItems), $mandatory_doc)) == count($mandatory_doc)) {
                        $this->recCandidateTrackingRepository->saveTracking($candidateId, "tax_forms_completed", false, $job_details->job_id);
                    }
                }*/
                if ($result) {
                    $msg = 'Success';
                    $status = 200;
                } else {
                    $msg = 'Failed';
                    $status = 400;
                }
            }
            DB::commit();
            $msg = 'Done';
            $status = 200;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $status = 400;
        }
        return response()->json(['message' => $msg], $status);
    }


    public function updateTrackingOnDocumentUpdate($tabid)
    {
        try {
            DB::beginTransaction();
            $candidateId = Auth::user()->id;
            $job_details = RecCandidateJobDetails::select('job_id')->where('status', 3)->where('candidate_id', $candidateId)->first();
            if ($tabid == 6) {
                    $this->recCandidateTrackingRepository->saveTracking($candidateId, "enrollment_completed", false, $job_details->job_id);
            } elseif ($tabid == 7) {
                   $this->recCandidateTrackingRepository->saveTracking($candidateId, "security_clearance_completed", false, $job_details->job_id);
            } elseif ($tabid == 8) {
                   $this->recCandidateTrackingRepository->saveTracking($candidateId, "tax_forms_completed", false, $job_details->job_id);
            } elseif ($tabid == 9) {
                $this->recCandidateTrackingRepository->saveTracking($candidateId, "upload_attachments", false, $job_details->job_id);
            }
             DB::commit();
             $msg = 'Success';
             $status = 200;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $status = 400;
        }
        return response()->json(['message' => $msg], $status);
    }
    

    public function storeCandidateAttachment(Request $request)
    {
        try {
            DB::beginTransaction();
            $uploadedItems = array();
            $required_attachment = array();
            $candidateId = Auth::user()->id;
            $attachment = $request->get('docdata');
            $job_details = RecCandidateJobDetails::select('job_id')->where('status', 3)->where('candidate_id', $candidateId)->first();
            $result = $this->recCandidateAttachmentRepository->storeAttachment($attachment['id'], $candidateId, $attachment['filename']);
          /*  $job = RecJob::find($job_details->job_id);
            $required_attachment = json_decode($job->required_attachments);
            if ($required_attachment != null) {
                $mandatory_doc = array_unique($required_attachment);
                $uploadedItems = RecCandidateAttachment::where('candidate_id', $candidateId)->pluck('attachment_id')->toArray();
                if (count(array_intersect(array_unique($uploadedItems), $mandatory_doc)) == count($mandatory_doc)) {
                    $this->recCandidateTrackingRepository->saveTracking($candidateId, "upload_attachments", false, $job_details->job_id);
                }
            } else {
                $this->recCandidateTrackingRepository->saveTracking($candidateId, "upload_attachments", false, $job_details->job_id);
            }*/
            if ($result) {
                $msg = 'Success';
                $status = 200;
            } else {
                $msg = 'Failed';
                $status = 400;
            }
            DB::commit();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $status = 400;
        }
        return response()->json(['message' => $msg], $status);
    }

    public function errorResponse($e)
    {
        \Log::error($e);
        return response(array(
            'success' => false,
            'message' =>
                (config('app.debug')) ?
                    ($e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()) :
                    ("Something Went Wrong")
        ), 500);
    }

    public function getTraining($course_type, Request $request)
    {
        $candidateId = Auth::user()->id;
        $training_user = TrainingUser::where('model_id', $candidateId)->first();
        $lookups['todo_count'] = $this->user_courses->getTodoCount($training_user->id);
        $lookups['recommended_count'] = $this->user_courses->getRecommendedCount($training_user->id);
        $lookups['completed_count'] = $this->user_courses->getCompletedCount($training_user->id);
        $lookups['over_due_count'] = $this->user_courses->getOverDueCountCount($training_user->id);
        $lookups['total_course_library'] = $this->user_courses->getCourseLibraryCount($training_user->id);
        $lookups['recent_achievements'] = $this->user_courses->getCompletedCourses($training_user->id);
        $course_name = $request->get('search_key');
        $course_list = $this->user_courses->getDashboardData($course_type, $course_name, $training_user->id);
        $course_lists = $this->user_courses->getListCourse($course_list, $training_user->id);
        return response()->json(['content' => $lookups, 'course' => $course_lists]);
    }

    public function viewCourse($id)
    {
        $candidateId = Auth::user()->id;
        $training_user = TrainingUser::where('model_id', $candidateId)->first();
        $data['courseDet'] = $this->trainingCourseRepository->get($id);
        $data['courseContents'] = $this->courseContentRepository->getContentByCourse($id);
        $userCourseDet = $this->user_courses->getUserCourseDetByCourse($id, $training_user->id);
        $data['course_rating'] = $this->trainingCourseUserRatingRepository->getRatingByCourseId($id);
        $data['count_of_active_test'] = $this->trainingTestSettingsRepository->hasActiveTest($id);
        $active_test = $this->trainingTestSettingsRepository->getActiveTest($id);
        $inputs['user_id'] = $candidateId;
        $inputs['training_course_id'] = $id;
        $inputs['status'] = 1;
        $data['attempt_history'] = $this->testUserResultRepository->getAllBasedOnFilters($inputs);
        if (isset($active_test)) {
            // $inputs['last_one'] = true;
            // $inputs['user_id'] = \Auth::user()->id;
            // $inputs['is_exam_pass'] = true;
            // $inputs['training_course_id'] = $id;
            $data['exam_results'] = $this->testUserResultRepository->getResultByCourseId($id, $training_user->id);

            //$exam_results=$this->testUserResultRepository->getStatus($id,$active_test->id);
        } else {
            $data['exam_results'] = null;
        }
        if (isset($userCourseDet['completed_percentage'])) {
            $circleBar['value'] = $userCourseDet['completed_percentage'] / 100;
            $circleBar['perc'] = $userCourseDet['completed_percentage'];
        } else {
            $circleBar['value'] = 1 / 100;
            $circleBar['perc'] = 1;
        }
        $data['circleBar'] = $circleBar;
        $data['has_previous_attempt'] = $this->testUserResultRepository->previousAttemptExist($id, $training_user->id);
        return response()->json(['success' => true, 'content' => $data]);
        // return view('learningandtraining::learner.course', compact('courseDet', 'courseContents', 'circleBar', 'course_rating', 'count_of_active_test', 'exam_results', 'has_previous_attempt'));
    }

    public function pdfView($id)
    {
        $courseContentsDet = $this->courseContentRepository->get($id);
        $courseContentsNextDet = $this->courseContentRepository->getNextContentByCourse($courseContentsDet->course_id, $id);
        if (isset($courseContentsNextDet[0])) {
            $data['nextContentId'] = $courseContentsNextDet[0]->id;
            $data['nextContentType'] = $courseContentsNextDet[0]->content_type_id;
        } else {
            $data['nextContentId'] = 0;
            $data['nextContentType'] = 0;
        }
        $data['courseContentsDet'] = $courseContentsDet;
        return response()->json(['success' => true, 'content' => $data]);
    }

    public function videoView($id)
    {
        $courseContentsDet = $this->courseContentRepository->get($id);
        $courseContentsNextDet = $this->courseContentRepository->getNextContentByCourse($courseContentsDet->course_id, $id);
        if (isset($courseContentsNextDet[0])) {
            $data['nextContentId'] = $courseContentsNextDet[0]->id;
            $data['nextContentType'] = $courseContentsNextDet[0]->content_type_id;
        } else {
            $data['nextContentId'] = 0;
            $data['nextContentType'] = 0;
        }
        $data['courseContentsDet'] = $courseContentsDet;
        return response()->json(['success' => true, 'content' => $data]);
    }

    public function imageView($id)
    {

        $courseContentsDet = $this->courseContentRepository->get($id);
        $courseContentsNextDet = $this->courseContentRepository->getNextContentByCourse($courseContentsDet->course_id, $id);
        if (isset($courseContentsNextDet[0])) {
            $data['nextContentId'] = $courseContentsNextDet[0]->id;
            $data['nextContentType'] = $courseContentsNextDet[0]->content_type_id;
        } else {
            $data['nextContentId'] = 0;
            $data['nextContentType'] = 0;
        }
        $data['courseContentsDet'] = $courseContentsDet;
        return response()->json(['success' => true, 'content' => $data]);
    }


    public function getTest($id)
    {
        $candidateId = Auth::user()->id;
        $training_user = TrainingUser::where('model_id', $candidateId)->first();
        $result = $this->testUserResultRepository->getResultByCourseId($id, $training_user->id);
        if (!empty($result)) {
            return $this->trainingTestRepository->getResultDetailsArr($result->id, $training_user->id);
        }

        $content['courseDet'] = $this->trainingCourseRepository->get($id);
        $userCourseDet = $this->user_courses->getUserCourseDetByCourse($id, $training_user->id);
        $content['course_rating'] = $this->trainingCourseUserRatingRepository->getRatingByCourseId($id);

        $content['examInputs'] = $this->trainingTestRepository->getExamQuestions($id, $training_user->id);

        $content['examSetting'] = $this->trainingTestSettingsRepository->getActiveSettingByCourse($id);
        if (isset($userCourseDet['completed_percentage'])) {
            $circleBar['value'] = $userCourseDet['completed_percentage'] / 100;
            $circleBar['perc'] = $userCourseDet['completed_percentage'];
        } else {
            $circleBar['value'] = 1 / 100;
            $circleBar['perc'] = 1;
        }
        $content['circleBar'] = $circleBar;
        $content['id'] = $id;
        return response()->json(['success' => true, 'content' => $content]);
    }

    public function storeTestResults(Request $request)
    {
        $candidateId = Auth::user()->id;
        $training_user = TrainingUser::where('model_id', $candidateId)->first();
        return $this->trainingTestRepository->save($request, $training_user->id);
    }

    public function storeUserRating(Request $request)
    {
        $candidateId = Auth::user()->id;
        $training_user = TrainingUser::where('model_id', $candidateId)->first();
        $result = $this->trainingCourseUserRatingRepository->save($request->all(), $training_user->id);
        if ($result['created']) {
            $success = 'true';
        } else {
            $success = 'false';
        }
        return response()->json(array('success' => $success));
    }

    public function showAllResults($course_id)
    {
        $candidateId = Auth::user()->id;
        $training_user = TrainingUser::where('model_id', $candidateId)->first();
        $inputs['training_user_id'] = $training_user->id;
        $inputs['training_course_id'] = $course_id;
        $inputs['status'] = 1;
        $result = $this->testUserResultRepository->getAllBasedOnFilters($inputs);
        return response()->json(['success' => true, 'content' => $result]);
    }

    public function getResultDetailById($id)
    {
        $candidateId = Auth::user()->id;
        $training_user = TrainingUser::where('model_id', $candidateId)->first();
        $inputs['last_one'] = true;
        $inputs['training_user_id'] = $training_user->id;
        $inputs['ids'] = $id;
        $content['result'] = $this->testUserResultRepository->getAllBasedOnFilters($inputs);
        $content['courseDet'] = $this->trainingCourseRepository->get($content['result']['training_course_id']);
        $userCourseDet = $this->user_courses->getUserCourseDetByCourse($content['result']['training_course_id'], $training_user->id);
        $content['course_rating'] = $this->trainingCourseUserRatingRepository->getRatingByCourseId($content['result']['training_course_id']);
        if (isset($userCourseDet['completed_percentage'])) {
            $circleBar['value'] = $userCourseDet['completed_percentage'] / 100;
            $circleBar['perc'] = $userCourseDet['completed_percentage'];
        } else {
            $circleBar['value'] = 1 / 100;
            $circleBar['perc'] = 1;
        }
        $content['circleBar'] = $circleBar;
        return response()->json(['success' => true, 'content' => $content]);
    }

    public function contentUpdate(Request $request)
    {
        \DB::beginTransaction();
        $candidateId = Auth::user()->id;
        $training_user = TrainingUser::where('model_id', $candidateId)->first();
        $trainingUserContent = $this->trainingUserContentRepository->save($request->all(), $training_user->id);
        \DB::commit();
        $content_id = $request->get('content_id');
        $contentsDet = $this->courseContentRepository->get($content_id);
        $contentsCount = $this->courseContentRepository->getCountByCourseId($contentsDet->course_id);
        $completedCourseContentCount = $this->courseContentRepository->getCompletedContentCountByCourseId($contentsDet->course_id, $training_user->id);
        $completedCoursePercentage = ($completedCourseContentCount / $contentsCount) * 100;
        $userCourseData['course_id'] = $contentsDet->course_id;
        $userCourseData['completed_percentage'] = round($completedCoursePercentage, 2);
        $count_of_active_test = $this->trainingTestSettingsRepository->hasActiveTest($contentsDet->course_id);
        if ($completedCoursePercentage == 100 && $count_of_active_test == 0) {
            $userCourseData['completed'] = 1;
            $userCourseData['completed_date'] = Carbon::now();
            $completed = "true";
        } else {
            $completed = "false";
        }
        $userCourseData['training_user_id'] = $training_user->id;
        $userCourse = $this->user_courses->save($userCourseData);
        $mandatory_course_arr=TrainingTeamCourseAllocation::where('team_id', config('globals.rec_training_id'))->where('mandatory', 1)->whereHas('training_course', function ($q) {
            $q->where('status', 1);
        })->pluck('course_id')->toArray();
        $candidate_completed=$this->user_courses->getCompletedCourseCount($training_user->id);
        if ($candidate_completed>=count($mandatory_course_arr)) {
            $deleteOldCandidateTracking=$this->recCandidateTrackingRepository->deleteOldCandidateTracking($candidateId, "core_training_completed");
            $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 3)->where('candidate_id', '=', $training_user->model_id)->first();
            if ($job_details) {
                 $this->recCandidateTrackingRepository->saveTracking($candidateId, "core_training_completed", false, $job_details->job_id);
            } else {
                $this->recCandidateTrackingRepository->saveTracking($candidateId, "core_training_completed", false);
            }
        }
        return response()->json(array('success' => 'true', 'data' => $userCourse, 'completed' => $completed));
    }


    public function updateFormComplete(Request $request)
    {
        try {
            $request->candidate_id = Auth::user()->id;
            $this->recCandidateRepository->updateFormComplete($request);
            $msg = 'Done';
            $success = true;
        } catch (\Exception $e) {
            $msg = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            $success = false;
        }
        return response()->json(array('success' => $success, 'message' => $msg));
    }

    public function getS3SignedUrl($fileName, $prefix)
    {
        $givenFileName = urldecode($fileName);
        //  $prefix = 'uof';
        return $this->s3HelperService->S3PreUpload(360, $givenFileName, $prefix, true);
    }

    public function postalCodeResolve($address)
    {
        $postalCode = $this->locationService->urlEncodeCnPostalCode($address);
        return response($this->locationService->getLatLongByAddress($postalCode));
    }
}
