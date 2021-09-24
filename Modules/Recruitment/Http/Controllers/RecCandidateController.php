<?php

namespace Modules\Recruitment\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\Languages;
use Modules\Documents\Models\Document;
use Modules\Recruitment\Models\RecJob;
use Illuminate\Support\Facades\Session;
use Modules\Admin\Models\EmailTemplate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repositories\AttachmentRepository;
use Modules\Recruitment\Models\RecCandidate;
use Modules\Admin\Models\JobPostFindingLookup;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Models\EmailNotificationType;
use Modules\Admin\Models\TrackingProcessLookup;
use Modules\Recruitment\Models\RecProcessSteps;
use Modules\Recruitment\Models\RecBrandAwareness;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Recruitment\Models\RecFeedbackLookups;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Recruitment\Models\RecSecurityAwareness;
use Modules\Recruitment\Models\RecUseOfForceLookups;
use Modules\Recruitment\Models\RecCandidateAwareness;
use Modules\Recruitment\Models\RecCandidateDocuments;
use Modules\Recruitment\Models\RecCandidateJobDetails;
use Modules\Recruitment\Repositories\RecJobRepository;
use Modules\Admin\Models\SecurityGuardLicenceThreshold;
use Modules\Documents\Repositories\DocumentsRepository;
use Modules\Recruitment\Models\RecCandidateUniformSize;
use Modules\Admin\Repositories\FeedbackLookupRepository;
use Modules\Recruitment\Models\RecCandidateAvailability;
use Modules\Recruitment\Models\RecCandidateScreeningQuestionLookups;
use Modules\Recruitment\Models\RecUniformMeasurementPoint;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Recruitment\Models\RecCandidateAttachmentLookup;
use Modules\Recruitment\Repositories\RecCandidateRepository;
use Modules\Recruitment\Models\RecCandidateScreeningQuestion;
use Modules\Recruitment\Models\RecMyersBriggsPersonalityType;
use Modules\Recruitment\Models\RecCandidateTransitionAttachment;
use Modules\Recruitment\Repositories\RecFeedbackLookupRepository;
use Modules\Recruitment\Http\Requests\RecEmployeeConversionRequest;
use Modules\Recruitment\Models\RecCandidateScreeningOtherLanguages;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Recruitment\Http\Requests\RecCandidateCredentialRequest;
use Modules\Recruitment\Repositories\RecCandidateTrackingRepository;
use Modules\Recruitment\Repositories\RecJobDocumentAllocationRepository;
use Modules\Recruitment\Repositories\RecCommissionairesUnderstandingLookupRepository;
use Modules\Recruitment\Models\RecCandidateUniformShippmentDetail;
use Modules\LearningAndTraining\Models\TrainingUser;
use Modules\LearningAndTraining\Models\TrainingUserCourseAllocation;
use Modules\LearningAndTraining\Models\TrainingUserTeam;
use Modules\LearningAndTraining\Models\TestUserResult;
use Modules\LearningAndTraining\Models\TrainingCourseUserRating;
use Modules\LearningAndTraining\Models\TrainingUserContent;
use Modules\Admin\Models\Employee;
use Illuminate\Support\Facades\Config;

const  ONBOARDING_STATUS = 3;

class RecCandidateController extends Controller
{
    protected $recCandidateRepository,
    $customerRepository,
    $userRepository,
    $customerEmployeeAllocationRepository,
    $attachmentRepository,
    $documentRepository,
    $feedbackLookupRepository,
    $helperService;

    public function __construct(
        RecCandidateRepository $recCandidateRepository,
        CustomerRepository $customerRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        AttachmentRepository $attachmentRepository,
        DocumentsRepository $documentRepository,
        RecFeedbackLookupRepository $recFeedbackLookupRepository,
        RecCommissionairesUnderstandingLookupRepository $recCommissionairesUnderstandingLookupRepository,
        HelperService $helperService,
        RecJobDocumentAllocationRepository $recJobDocumentAllocationRepository,
        RecCandidateTrackingRepository $recCandidateTrackingRepository
    ) {
        $this->recCandidateRepository = $recCandidateRepository;
        $this->customerRepository = $customerRepository;
        $this->userRepository = new UserRepository();
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->employee_allocation = new EmployeeAllocationRepository();
        $this->attachmentRepository = $attachmentRepository;
        $this->documentRepository = $documentRepository;
        $this->recFeedbackLookupRepository = $recFeedbackLookupRepository;
        $this->recCommissionairesUnderstandingLookupRepository = $recCommissionairesUnderstandingLookupRepository;
        $this->helperService = $helperService;
        $this->recCandidateRepository = $recCandidateRepository;
        $this->recJobDocumentAllocationRepository = $recJobDocumentAllocationRepository;
        $this->recCandidateTrackingRepository = $recCandidateTrackingRepository;
    }

    /**
     * Showing Candidate mapping
     *
     * @param Request $request
     * @return view
     */
    public function mapping(Request $request)
    {
        return view('recruitment::mapping');
    }

    /**
     * Showing client and candidates in google map
     *
     * @param [type] $job_id
     * @return void
     */
    public function plotJobCandidatesMap($job_id, Request $request)
    {

        //$result=RecCandidateJobDetails::where('job_id', $job_id)->pluck('candidate_id')->toArray();
        $request->request->add(['job_id' => $job_id]); //add request
        $candidates = $this->recCandidateRepository->getCandidates(null, $request);
        $job = RecJob::with(['customer', 'experiences', 'assignee'])->find($job_id);
        $lookups['client_name'] = $this->customerRepository->getList(PERMANENT_CUSTOMER);
        $personalityTypes = RecMyersBriggsPersonalityType::pluck('type', 'type')->toArray();
        //$trackingProcess = TrackingProcessLookup::select(\DB::raw("CONCAT(step_number, ' - ',process_steps) as candidate_stage"), 'id')->pluck('candidate_stage', 'id')->toArray();
        $trackingProcess = RecProcessSteps::select(\DB::raw("CONCAT(step_order, ' - ', display_name) as candidate_stage"), 'id')->where('step_order', '>=', 9)->where('step_order', '!=', config('globals.training_completed_tracking_id'))->pluck('candidate_stage', 'id')->toArray();
        return view('recruitment::candidates-in-map', compact('candidates', 'job', 'lookups', 'request', 'personalityTypes', 'trackingProcess'));
    }

    /**
     * To view the candidate application and candidate details
     *
     * @param [type] $candidate_id
     * @param [type] $job_id
     * @return void
     */
    public function viewCandidate($candidate_id)
    {
        $candidate = $this->recCandidateRepository->getJobApplicationOfCandidate($candidate_id);
        $availabilities = RecCandidateAvailability::where([['candidate_id', $candidate->id]])->first();
        $decoded_days = html_entity_decode($availabilities->days_required);
        $decoded_shifts = html_entity_decode($availabilities->shifts);
        $days_required =  array_map('trim', explode(',', $decoded_days));
        $shifts =array_map('trim', explode(',', $decoded_shifts));

        $session_obj['job'] = $candidate->job;
        $brand_awareness_collection = RecBrandAwareness::orderby('order_sequence', 'asc')->pluck('answer', 'id')->toArray();
        $session_obj['brand_awareness'] = $brand_awareness_collection;
        $security_awareness_collection = RecSecurityAwareness::orderby('order_sequence', 'asc')->get()->pluck('answer', 'id')->toArray();
        $session_obj['security_awareness'] = $security_awareness_collection;
        Session::put('CANINFO', $session_obj);
        $lookups = $this->getLookups();
        $position_experience = json_decode($candidate->guardingexperience->positions_experinces, true);
        $event_log = $this->recCandidateRepository->getScheduleEventLogs($candidate_id);
        $job_onboarded = RecCandidateJobDetails::where('candidate_id', $candidate_id)->where('status', ONBOARDING_STATUS)->with('candidate.attachements')->first();
        $otherlanguages = RecCandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)->get();
        $languages = Languages::get();
        return view(
            'recruitment::view-candidate-application',
            compact(
                'candidate',
                'session_obj',
                'lookups',
                'position_experience',
                'event_log',
                'days_required',
                'shifts',
                'job_onboarded',
                'otherlanguages',
                'languages'
            )
        );
    }

    /**
     * A list of candidates those are in last tracking step listed
     *
     * @param Request $request
     * @return boolean
     */
    public function candidateConversion(Request $request)
    {
        $page_variables_arr = $this->userRepository->userIndex();
        $roles = array_diff($page_variables_arr['roles'], ["Ceo", "Cfo", "Vice President", "Admin", "Coo", "Customer", "Client"]);
        // $roles = $page_variables_arr['roles'];
        $today = Carbon::now();
        $date = $today->toDateString();
        $work_types = $page_variables_arr['work_types'];
        $employees = $page_variables_arr['employees'];
        $security_clearances = $page_variables_arr['security_clearances'];
        $positions = $page_variables_arr['positions'];
        $certificates = $page_variables_arr['certificates'];
        $customer = Customer::get()->pluck('client_name_and_number', 'id')->toArray();
        //$documentCategoryDetails = DocumentCategory::where('document_category', 'Agreement')->first();
        //$documentNameDetails = DocumentNameDetail::where('name', 'Signed Employee Contract')->first();
        $documentTypeID = EMPLOYEE;
        $approversList = $this->userRepository->getUserList(true, null, null, ['super_admin'])->sortBy('full_name')->pluck('full_name', 'id')->toArray();
        $banks = $page_variables_arr['banks'];
        $bank_code = $page_variables_arr['bank_code'];
        $payment_methods = $page_variables_arr['payment_methods'];
        $marital_status = $page_variables_arr['marital_status'];
        $salutation = $page_variables_arr['salutation'];
        $payroll_group = $page_variables_arr['payroll_group'];
        $relation = $page_variables_arr['relation'];
        $customers = $page_variables_arr['customers'];
        //dd($roles, $employees, $work_types, $security_clearances, $positions, $customer, $date, $certificates, $approversList, $documentCategoryDetails, $documentNameDetails, $documentTypeID);
        return view('recruitment::conversion-list', compact(
            'roles',
            'employees',
            'work_types',
            'security_clearances',
            'positions',
            'customer',
            'date',
            'certificates',
            'approversList',
            //'documentCategoryDetails',
            //'documentNameDetails',
            'documentTypeID',
            'bank_code',
            'banks',
            'payment_methods',
            'payroll_group',
            'customers',
            'marital_status',
            'relation'
        ));
    }

    /*
     *Function to populate  candidates those are in last tracking step listed
     *
     * @param null
     * @return json
     */
    public function candidateConversionList()
    {
        $tracking_summary = $this->recCandidateRepository->getCandidateConversionList();
        return datatables()->of($tracking_summary)->toJson();
    }

    public function showDetails($id)
    {
        $user_details = $this->recCandidateRepository->getCandidateDetails($id);
        if (is_object($user_details)) {
            if (is_numeric($user_details->candidate->guardingExperience->years_security_experience)) {
                $employee_data['years_of_security'] = (int) $user_details->candidate->guardingExperience->years_security_experience;
            } else {
                $employee_data['years_of_security'] = '';
            }
            if (is_numeric($user_details->candidate->securityclearance->years_lived_in_canada)) {
                $now = Carbon::now();
                $ylInCa = $now->subYear($user_details->candidate->securityclearance->years_lived_in_canada);
                $employee_data['being_canada_since'] = $ylInCa->format('Y-m-d');
            }
        }
        return response()->json(array('user' => $user_details, 'employee_data' => $employee_data));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function Employeestore(RecEmployeeConversionRequest $request, $module)
    {
        try {
            \DB::beginTransaction();
            $user_store = $this->userRepository->userStore($request);
            $candidate_id = $request->get('candidate_id');
            $candidateData['is_converted']=1;
            $candidateData['id']=$request->get('candidate_id');
            $convertedFlag=$this->recCandidateRepository->updateCandidateCredentials($candidateData);
            $mapping_id = $this->recCandidateRepository->candidateEmployeeMapping($user_store, $candidate_id);
            $employee_id_list[] = json_decode($user_store->id);
            $customer_id = $request->get('project_no');
            $allocation = $this->customerEmployeeAllocationRepository->allocateEmployee($employee_id_list, $customer_id, $request);
            $latest_data = Customer::with('employeeLatestCustomerSupervisor', 'employeeLatestCustomerAreaManager')->where('id', $customer_id)->get();
            $supervisor = data_get($latest_data, '*.employeeLatestCustomerSupervisor');
            $areamanager = data_get($latest_data, '*.employeeLatestCustomerAreaManager');
            if ($supervisor[0] != null) {
                $supervisor_id = $supervisor[0]->user_id;
                $allocation = $this->employee_allocation->userAllocation($employee_id_list, $supervisor_id);
            }
            if ($areamanager[0] != null) {
                $areamanager_id = $areamanager[0]->user_id;
                $allocation = $this->employee_allocation->userAllocation($employee_id_list, $areamanager_id);
            }
            $file = $this->attachmentRepository->saveAttachmentFile($module, $request);
            $transition_attachment['transition_id'] = $mapping_id;
            $transition_attachment['attachment_id'] = $file['file_id'];
            RecCandidateTransitionAttachment::create($transition_attachment);
            $transation_documet = [
            'user_id' => $user_store->id,
            'document_type_id' => $request->document_type_id,
            'document_category_id' => $request->document_category_id,
            'document_name_id' => $request->document_name_id,
            ];
            $transation_documet['answer_type'] = $this->documentRepository->getCategoryModels(null, $request->document_category_id);
            $transation_documet['attachment_id'] = $file['file_id'];
            $transation_documet['created_by'] = \Auth::id();
            Document::create($transation_documet);
            $job_details = RecCandidateJobDetails::where('status', '=', 3)->where('candidate_id', '=', $candidate_id)->with('candidate.attachements.attachment')->first();
            $this->recCandidateTrackingRepository->saveTracking($candidate_id, "candidate_conversion", false, $job_details->job_id);
            $trainingUserDetails=TrainingUser::where('model_id', $candidate_id)->first();
            TrainingUserCourseAllocation::where('training_user_id', $trainingUserDetails->id)->update(['user_id'=>$user_store->id]);
            TrainingUserTeam::where('training_user_id', $trainingUserDetails->id)->update(['user_id'=>$user_store->id]);
            TestUserResult::where('training_user_id', $trainingUserDetails->id)->update(['user_id'=>$user_store->id]);
            TrainingCourseUserRating::where('training_user_id', $trainingUserDetails->id)->update(['user_id'=>$user_store->id]);
            TrainingUserContent::where('training_user_id', $trainingUserDetails->id)->update(['user_id'=>$user_store->id]);
        
            // To add recruitment documents to employee documents
            $documents = $this->recJobDocumentAllocationRepository->getCandidateDocuments($candidate_id, $job_details->job_id);
            if (!empty($documents)) {
                foreach ($documents as $details) {
                    if ($details['document_job']['id'] != null) {
                        $document_details = [
                        'user_id' => $user_store->id,
                        'document_type_id' => 1,
                        'document_category_id' => config('globals.document_category_id'),
                        'document_name_id' => config('globals.document_name_id'),
                        'document_description' => $details['document_allocation_with_trashed']['document_name']
                        ];
                        $document_details['answer_type'] = "RecDocument";
                        $document_details['attachment_id'] = $details['document_job']['id'];
                        $document_details['created_by'] = $user_store->id;
                        Document::create($document_details);
                    }
                }
            }
            
            if ($job_details->candidate->attachements != null) {
                foreach ($job_details->candidate->attachements as $attachements) {
                    $document_details = [
                        'user_id' => $user_store->id,
                        'document_type_id' => 1,
                        'document_category_id' => config('globals.document_category_id'),
                        'document_name_id' => config('globals.document_name_id'),
                        'document_description' => ($attachements->attachment != null) ? $attachements->attachment->attachment_name : 'Recruitment Document'
                        ];
                        $document_details['answer_type'] = "RecAttachment";
                        $document_details['attachment_id'] =$attachements->id;
                        $document_details['created_by'] = $user_store->id;
                        Document::create($document_details);
                }
            }

             // To create user profile image
            if ((\File::exists(public_path('images/uploads')."/".$candidate_id."_candidate_profile.png")) && (!\File::exists(public_path('images/uploads')."/".$user_store->id."_profile.png"))) {
                copy(public_path('images/uploads')."/".$candidate_id."_candidate_profile.png", public_path('images/uploads')."/".$user_store->id."_profile.png");
                Employee::updateOrCreate(['user_id' => $user_store->id], ['image'=>$user_store->id."_profile.png"]);
            }
        
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            \DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /*
     *Function to upload candidate image
     *
     * @param  $candidate_id
     * @return
     */
    public function uploadProfileImage(Request $request, $candidate_id)
    {
        try {
            $candidate = RecCandidate::find($candidate_id);
            if (!empty($candidate)) {
                $candidateImageName = $this->userRepository->uploadProfileImage($request, $candidate_id, 'candidate_profile');
                if ($candidateImageName != null) {
                    $insertCandidate = $candidate->toArray();
                    $insertCandidate['profile_image'] = $candidateImageName;
                    RecCandidate::updateOrCreate(['id' => $candidate_id], $insertCandidate);
                    \DB::commit();
                    return response()->json(['success' => true, 'image' => $candidateImageName, 'message' => 'Image uploaded successfully.']);
                }
            }
            return response()->json(['success' => false, 'message' => 'Failed to upload image.']);
        } catch (Exception $e) {
            \DB::rollback();
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    public function candidateSummary()
    {
        $feedbackLookups = $this->recFeedbackLookupRepository->getList();
        $tracking_users_arr = $this->recCandidateRepository->hrTrackingList(\Auth::user(), 'Proceed')->toArray();
        return view('recruitment::candidate-summary', compact('feedbackLookups', 'tracking_users_arr'));
    }

    /**
     *  Get the candidate list
     *
     * @param null
     * @return json
     */
    public function candidateSummaryList()
    {
        //$candidates = RecCandidate::get();

        // $candidates = $this->recCandidateRepository->getCandidates(
        //     $candidate_selection_status = null,
        //     $request = null,
        //     $type_of_records_request = null,
        //     $order_by = 'name',
        //     $customer_session = true
        // );
        $candidates=$this->recCandidateRepository->getCandidateSummaryList();

        return datatables()->of($candidates)->toJson();
    }

    /**
     * Archive Row
     *
     * @param Request $request
     * @return json
     */
    public function archive(Request $request)
    {
        return $this->recCandidateRepository->deleteCandidateJobs($request);
    }

     /**
     * Update Job Status
     *
     * @param Request $request
     * @return json
     */
    public function updateCandidateJobStatus(Request $request)
    {
        return $this->recCandidateRepository->updateJobStatus($request);
    }

    public function candidateCredentials()
    {
        $notificationType=EmailNotificationType::where('type', 'rec_candidate_register_email_script')->first();
        $email_template=EmailTemplate::where('type_id', $notificationType->id)->first();
        $mail_content['body']= $email_template['email_body'];
        return view('recruitment::candidate-credentials', compact('mail_content'));
    }
     /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCandidateCredentialsList()
    {
        return datatables()->of($this->recCandidateRepository->getAllCandidateCredentials())->addIndexColumn()->toJson();
    }
     /**
     * Display a listing of resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCandidateTrackingList()
    {
        return datatables()->of($this->recCandidateRepository->getAllCandidateTrackings())->addIndexColumn()->toJson();
    }

    /**
     * Display details of single resource
     *
     * @param $id
     * @return json
     */
    public function getSingleCandidateCredential($id)
    {
        return response()->json($this->recCandidateRepository->getCandidateCredential($id));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function storeCandidateCredentials(RecCandidateCredentialRequest $request)
    {
        try {
            \DB::connection('mysql_rec')->beginTransaction();
            $lookup = $this->recCandidateRepository->saveCandidateCredentials($request->all(), $request);
            \DB::connection('mysql_rec')->commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::connection('mysql_rec')->rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function destroyCandidateCredential($id)
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->recCandidateRepository->deleteCandidateCredential($id);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function loginReminderMail()
    {
        try {
            \DB::beginTransaction();
            $lookup_delete = $this->recCandidateRepository->loginRemainder();
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /*
     *Function for Lookups
     *
     * @param $job_id
     * @return array
     */
    public function getLookups()
    {
        $session_obj = Session::get('CANINFO');
        $lookups['positions_lookups'] = \DB::table('position_lookups')
        ->whereNull('deleted_at')
        ->orderBy('position', 'ASC')
        ->pluck('position', 'id')
        ->toArray();
        $lookups['employee_ratings'] = \DB::table('employee_rating_lookups')
            ->whereNull('deleted_at')
            ->orderBy('score', 'ASC')
            ->pluck('rating', 'id')
            ->toArray();
        $lookups['security_provider'] = \DB::table('security_provider_lookups')
        ->whereNull('deleted_at')
        ->orderBy('security_provider', 'ASC')
        ->pluck('security_provider', 'id')
        ->toArray();
        $lookups['skills_lookup'] = \DB::connection('mysql')->table('skill_lookups')
        ->whereNull('deleted_at')
        ->get();
        $lookups['languages_lookups'] = \DB::connection('mysql')->table('language_lookups')->whereNull('deleted_at')->get();
        $lookups['screening_questions'] = RecCandidateScreeningQuestionLookups::orderByRaw("FIELD(category , 'initiative','stress_tolerance','teamwork_interpersonal_group_dynamics','scenarios_problem_solving') ASC")->get();
        $lookups['uniformcontrolLookups'] = RecUniformMeasurementPoint::get();
        // $lookups['attachmentLookups'] = CandidateAttachmentLookup::where('job_id', null)->orWhere('job_id', $job_id)->get();
        $lookups['division'] = \DB::connection('mysql')->table('division_lookups')->whereNull('deleted_at')->pluck('division_name', 'id')->toArray();
        $lookups['smart_phones'] = \DB::connection('mysql')->table('smart_phone_types')->whereNull('deleted_at')->pluck('type', 'id')->toArray();
        $lookups['experience_ratings'] = \DB::connection('mysql_rec')->table('rec_rate_experience_lookups')->whereNull('deleted_at')->orderby('score', 'desc')->pluck('experience_ratings', 'id')->toArray();
        $lookups['commissionaires_understanding'] = $this->recCommissionairesUnderstandingLookupRepository->getList();
        $lookups['english_ratings'] = \DB::connection('mysql_rec')->table('rec_english_rating_lookups')->whereNull('deleted_at')->orderby('order_sequence', 'asc')->pluck('english_ratings', 'id')->toArray();
        $lookups['threshold'] = SecurityGuardLicenceThreshold::pluck('threshold')->toArray();
        $lookups['job_post_finding'] =JobPostFindingLookup::orderby('order_sequence', 'asc')->pluck('job_post_finding', 'id')->toArray();
        $lookups['force'] = RecUseOfForceLookups::orderby('order_sequence', 'asc')->get()->pluck('use_of_force', 'id')->toArray();
        return $lookups;
    }

     /*
     *Function to edit candidate screening details by super admin
     *
     * @param $candidate_id
     * @param $job_id
     * @return view
     */
    public function editCandidateJob($candidate_id, Request $request)
    {
        $lookups = $this->getLookups();
        $mandatory_items=array();
        $candidate = $this->recCandidateRepository->getJobApplicationOfCandidate($candidate_id);
        $position_experience = json_decode($candidate->guardingexperience->positions_experinces, true);
        $availabilities = RecCandidateAvailability::where([['candidate_id', $candidate->id]])->first();
        $decoded_days = html_entity_decode($availabilities->days_required);

        $decoded_shifts = html_entity_decode($availabilities->shifts);
        $days_required =  array_map('trim', explode(',', $decoded_days));
        $shifts =array_map('trim', explode(',', $decoded_shifts));
        // $session_obj['job'] = $candidateJob->job;
        $session_obj['candidate'] = $candidate;
        $brand_awareness_collection = RecBrandAwareness::orderby('order_sequence', 'asc')->pluck('answer', 'id')->toArray();
        $session_obj['brand_awareness'] = $brand_awareness_collection;
        $security_awareness_collection = RecSecurityAwareness::orderby('order_sequence', 'asc')->get()->pluck('answer', 'id')->toArray();
        $session_obj['security_awareness'] = $security_awareness_collection;
        $attachement_ids = $candidate->attachements->pluck('attachment_file_name', 'attachment_id')->toArray();
        //$session_obj['brand_awareness'] = CandidateBrandAwareness::select('answer','id')->get();
        $uniformdetails = RecCandidateUniformSize::where("candidate_id", $candidate_id)
             ->get()
             ->pluck("measurement_value", "measurement_id")
            ->toArray();
        Session::put('CANINFO', $session_obj);
        $candidateJob = RecCandidateJobDetails::where('candidate_id', $candidate_id)->where('status', ONBOARDING_STATUS)->with('candidate.attachements')->first();
        $documents['enrollment'] = [];
        $documents['securityclearance'] = [];
        $documents['taxforms'] = [];
        if (isset($candidateJob)) {
            $lookups['attachmentLookups'] = RecCandidateAttachmentLookup::where('job_id', null)->orWhere('job_id', $candidateJob->job_id)->get();
            $documents['enrollment'] = $this->recJobDocumentAllocationRepository->getDocumentApi($request->merge([
            'processTabId' => 6,
            'jobId' => $candidateJob->job_id,
            'candidateId' => $candidate->id,
            ]));
            $documents['securityclearance'] = $this->recJobDocumentAllocationRepository->getDocumentApi($request->merge([
            'processTabId' => 7,
            'jobId' => $candidateJob->job_id,
            'candidateId' => $candidate->id,
            ]));
            $documents['taxforms'] = $this->recJobDocumentAllocationRepository->getDocumentApi($request->merge([
            'processTabId' => 8,
            'jobId' => $candidateJob->job_id,
            'candidateId' => $candidate->id,
            ]));
        }
        //    $documents = $this->recJobDocumentAllocationRepository->getDocumentApi($request->merge([
        // 'processTabId' => 6,
        // 'jobId' => 5,
        //    ]));
        $access_key        = env('AWS_KEY');
        $secret_key         = env('AWS_SECRET');
        $my_bucket          = env('AWS_RECRUITMENT_BUCKET');
        $region             = env('S3_REGION');
        $short_date         = gmdate('Ymd'); //short date
        $iso_date           = gmdate("Ymd\THis\Z"); //iso format date
        $presigned_url_expiry    = 3600; //Presigned URL validity expiration time (3600 = 1 hour)

        $policy = array(
        'expiration' => gmdate('Y-m-d\TG:i:s\Z', strtotime('+6 hours')),
        'conditions' => array(
            array('bucket' => $my_bucket),
            array('acl' => 'private'),
            array('starts-with', '$key', ''),
            array('success_action_status' => '201'),
            array('x-amz-credential' => implode('/', array($access_key, $short_date, $region, 's3', 'aws4_request'))),
            array('x-amz-algorithm' => 'AWS4-HMAC-SHA256'),
            array('x-amz-date' => $iso_date),
            array('x-amz-expires' => ''.$presigned_url_expiry.''),
        ));

        // $success_redirect    = 'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; //URL to which the client is redirected upon success (currently self)
        //$expiration_date    = gmdate('Y-m-d\TG:i:s\Z', strtotime('+1 hours')); //policy expiration 1 hour from now
         $policybase64 = base64_encode(json_encode($policy));
         $kDate = hash_hmac('sha256', $short_date, 'AWS4' . $secret_key, true);
         $kRegion = hash_hmac('sha256', $region, $kDate, true);
         $kService = hash_hmac('sha256', "s3", $kRegion, true);
         $kSigning = hash_hmac('sha256', "aws4_request", $kService, true);
         $signature = hash_hmac('sha256', $policybase64, $kSigning);
         $otherlanguages = RecCandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)->get();
         $languages = Languages::get();
        $job_details = [];
        if (isset($candidateJob)) {
            $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', ONBOARDING_STATUS)->where('candidate_id', '=', $candidate_id)->first();
            $job=RecJob::find($job_details->job_id);
            $mandatory_items=json_decode($job->required_attachments);
        }
        return view('recruitment::candidate.edit-candidate-job', compact('candidate', 'session_obj', 'lookups', 'position_experience', 'days_required', 'shifts', 'attachement_ids', 'candidateJob', 'uniformdetails', 'policybase64', 'access_key', 'short_date', 'region', 'iso_date', 'presigned_url_expiry', 'signature', 'my_bucket', 'documents', 'otherlanguages', 'languages', 'mandatory_items'));
    }


     /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return json
     */
    public function documentStore(Request $request)
    {
        try {
            \DB::beginTransaction();
            $candidateId = Auth::user()->id;
            if ($request->items) {
                foreach ($request->items as $key => $document) {
                    RecCandidateDocuments::create([
                        'candidate_id' => $document['candidate_id'],
                        'rec_job_document_allocation_id' => $document['id'],
                        'file_name' => $document['filename']
                    ]);
                }
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /*
     *Function to store Screening question Score
     *
     * @param Request $request
     * @return json
     */
    public function reviewScreeningAnswers(Request $request)
    {
        $reviewScreening=$this->recCandidateRepository->reviewScreeningAns($request->all());
        return response()->json(array('success' => true));
    }

    /**
     * Remove  Candidate Attachment
     *
     * @param [type] $candidate_id
     * @param [type] $attachment_id
     * @return void
     */
    public function removeCandidateAttachment($candidate_id, $attachment_id)
    {
        return $this->recCandidateRepository->deleteCandidateAttachment($candidate_id, $attachment_id);
    }
    /**
     * Remove  Candidate Attachment
     *
     * @param [type] $candidate_id
     * @param [type] $attachment_id
     * @return void
     */
    public function removeCandidateDocument($candidate_id, $document_id)
    {
        try {
            RecCandidateDocuments::where(['candidate_id' => $candidate_id, 'rec_job_document_allocation_id' => $document_id])->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        };
    }

     /**
     * Remove  Candidate Attachment
     *
     * @param [type] $candidate_id
     * @param [type] $attachment_id
     * @return void
     */
    public function showResetPasswordTemplate(Request $request)
    {
        $notificationType=EmailNotificationType::where('type', 'rec_candidate_password_reset')->first();
        $email_template=EmailTemplate::where('type_id', $notificationType->id)->first();
        $mail_content['body']= $email_template['email_body'];
        return response()->json(array('success' => true, 'mail_content' => $mail_content,'candidate_id'=>$request->id));
    }

    public function sendPasswordResetMail(Request $request)
    {
        try {
            \DB::beginTransaction();
            $mailSend=$this->recCandidateRepository->sendPasswordResetMail($request->all());
            \DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    public function viewCandidateOnboarding($candidate_id, Request $request)
    {
        $candidate = $this->recCandidateRepository->getJobApplicationOfCandidate($candidate_id);
        $availabilities = RecCandidateAvailability::where('candidate_id', $candidate->id)->first();

        $decoded_days = html_entity_decode($availabilities->days_required);
        $decoded_shifts = html_entity_decode($availabilities->shifts);
        $days_required =  array_map('trim', explode(',', $decoded_days));
        $shifts =array_map('trim', explode(',', $decoded_shifts));

        $session_obj['job'] = $candidate->job;
        $brand_awareness_collection = RecBrandAwareness::orderby('order_sequence', 'asc')->pluck('answer', 'id')->toArray();
        $session_obj['brand_awareness'] = $brand_awareness_collection;
        $security_awareness_collection = RecSecurityAwareness::orderby('order_sequence', 'asc')->get()->pluck('answer', 'id')->toArray();
        $session_obj['security_awareness'] = $security_awareness_collection;
        Session::put('CANINFO', $session_obj);
        $lookups = $this->getLookups();
        $position_experience = json_decode($candidate->guardingexperience->positions_experinces, true);
        $event_log = $this->recCandidateRepository->getScheduleEventLogs($candidate_id);
        $candidateJob = RecCandidateJobDetails::where('candidate_id', $candidate_id)->where('status', ONBOARDING_STATUS)->with('candidate.attachements')->first();
        $uniformdetails = [];

        $uniformdetails = RecCandidateUniformSize::where("candidate_id", $candidate_id)
         ->get()
         ->pluck("measurement_value", "measurement_id")
        ->toArray();
        $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', ONBOARDING_STATUS)->where('candidate_id', '=', $candidate->id)->first();
        $documents['enrollment'] = $this->recJobDocumentAllocationRepository->getDocumentApi($request->merge([
            'processTabId' => 6,
            'jobId' => $candidateJob->job_id,
            'candidateId' => $candidate->id,
            ]));
        $documents['securityclearance'] = $this->recJobDocumentAllocationRepository->getDocumentApi($request->merge([
        'processTabId' => 7,
        'jobId' => $candidateJob->job_id,
        'candidateId' => $candidate->id,
         ]));
        $documents['taxforms'] = $this->recJobDocumentAllocationRepository->getDocumentApi($request->merge([
        'processTabId' => 8,
        'jobId' => $candidateJob->job_id,
        'candidateId' => $candidate->id,
         ]));
        return view('recruitment::view-candidate-onboarding-application', compact('candidateJob', 'candidate', 'session_obj', 'lookups', 'position_experience', 'event_log', 'days_required', 'shifts', 'uniformdetails', 'job_details', 'documents'));
    }


    /*
     *Function to download attachments
     *
     * @param  $file
     * @return
     */
    public function downloadForceDocument($file_id)
    {
        try {
            $path = $this->recCandidateRepository->forceAttachment($file_id);
            return response()->download($path['path'], $path['file'], []);
        } catch (\Exception $e) {
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
    }

    /**
     * Print candidate job document
     */
    public function printViewCandidateJob($id)
    {
        $candidateJob = RecCandidateAwareness::with(
            'candidate',
            'candidate_brand_awareness',
            'candidate_security_awareness',
            'personality_scores.score_type',
            // 'job',
            'candidate.addresses',
            'candidate.availability',
            'candidate.securityclearance',
            'candidate.guardingexperience',
            'candidate.securityproximity',
            'candidate.wageexpectation',
            'candidate.references',
            'candidate.educations',
            'candidate.experience',
            'candidate.miscellaneous',
            'candidate.languages',
            'candidate.languages.language_looukp',
            'candidate.employment_history',
            'candidate.screening_questions',
            'candidate.skills',
            'candidate.skills.skill_lookup',
            'candidate.experience.division',
            // 'candidate.interviewnote',
            'candidate.comissionaires_understanding',
            'candidate.uniform_measurements.uniform_measurement_points',
            'candidate.lastTrack',
            'candidate.awareness'
        )
            ->find($id);
            //dd($candidateJob->toArray());

        return view('recruitment::candidate.print-application', compact('candidateJob'));
    }

    public function candidateExport()
    {
        $candidateId = RecCandidate::where('is_completed', 1)->pluck('id');
        $results = RecCandidateAwareness::with([
            'candidate.wageexpectation' =>function ($query) {
                $query->with('wageprovider', 'lastrole', 'rating');
            }
        ])
            ->whereIn('candidate_id', $candidateId)
        ->with(
            'candidate',
            'candidate_brand_awareness',
            'candidate_security_awareness',
            'personality_scores.score_type',
            // 'job',
            'candidate.addresses',
            'candidate.availability',
            'candidate.securityclearance',
            'candidate.guardingexperience',
            'candidate.securityproximity',
            'candidate.references',
            'candidate.educations',
            'candidate.experience',
            'candidate.miscellaneous',
            'candidate.languages',
            'candidate.languages.language_looukp',
            'candidate.employment_history',
            'candidate.screening_questions',
            'candidate.skills',
            'candidate.skills.skill_lookup',
            'candidate.experience.division',
            // 'candidate.interviewnote',
            'candidate.comissionaires_understanding.candidateUnderstandingLookup',
            'candidate.uniform_measurements.uniform_measurement_points',
            'candidate.referalAvailibility.jobPostFinding',
            'candidate.technicalSummary',
            'candidate.competency_matrix',
            'candidate.force',
            'candidate.other_languages.language_lookup'
        )
            ->get();

        //dd($results->toArray()[15]);

        $excel_results = $this->recCandidateRepository->prepareCandidatesExcel($results);
        return $this->exportExcel($excel_results);
    }

    public function exportExcel($excel_results)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($excel_results);
        $writer = new Xlsx($spreadsheet);
        //$writer->save('hello world.xlsx'); // saved to folder
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="CandidateExport.xlsx"');
        $writer->save("php://output");
        exit;
    }

    public function getUniformDetailsOfCandidate($candidate_id)
    {

          $data['candidateDetails']=$this->recCandidateRepository->get($candidate_id);
          $data['uniformdetails'] = RecCandidateUniformSize::where("candidate_id", $candidate_id)
             ->get()
             ->pluck("measurement_value", "measurement_id")
            ->toArray();
        $data['address']=RecCandidateUniformShippmentDetail::where('candidate_id', $candidate_id)->first();
        return response()->json(array('success' => true, 'result' => $data));
    }

    public function storeCandidateSessionalData(Request $request)
    {
        $candidate_arr = $request->get('export_array');
        $customerId = $request->get('customerId');
        session_start();
        unset($_SESSION['geomapping_export_candidate_arr']);
        unset($_SESSION['geomapping_export_customerId']);
        $_SESSION['geomapping_export_candidate_arr'] = $candidate_arr;
        $_SESSION['geomapping_export_customerId'] = $customerId;
    }

    public function candidateGeomappingExport()
    {
        session_start();
        $candidate_arr = isset($_SESSION['geomapping_export_candidate_arr']) ? $_SESSION['geomapping_export_candidate_arr'] : null;
        $customerId = isset($_SESSION['geomapping_export_customerId']) ? $_SESSION['geomapping_export_customerId'] : null;
        $customerLoc = Customer::select('geo_location_lat', 'geo_location_long')->where('id', $customerId)->first();
       // $results = $this->recCandidateRepository->getCandidatesById($candidate_arr);
        $results = RecCandidateAwareness::with([
            'candidate.wageexpectation' =>function ($query) {
                $query->with('wageprovider', 'lastrole', 'rating');
            }
        ])
            ->whereIn('candidate_id', $candidate_arr)
        ->with(
            'candidate',
            'candidate_brand_awareness',
            'candidate_security_awareness',
            'personality_scores.score_type',
            // 'job',
            'candidate.addresses',
            'candidate.availability',
            'candidate.securityclearance',
            'candidate.guardingexperience',
            'candidate.securityproximity',
            'candidate.references',
            'candidate.educations',
            'candidate.experience',
            'candidate.miscellaneous',
            'candidate.languages',
            'candidate.languages.language_looukp',
            'candidate.employment_history',
            'candidate.screening_questions',
            'candidate.skills',
            'candidate.skills.skill_lookup',
            'candidate.experience.division',
            // 'candidate.interviewnote',
            'candidate.comissionaires_understanding.candidateUnderstandingLookup',
            'candidate.uniform_measurements.uniform_measurement_points',
            'candidate.referalAvailibility.jobPostFinding',
            'candidate.technicalSummary',
            'candidate.competency_matrix',
            'candidate.force',
            'candidate.other_languages.language_lookup'
        )
            ->get();
        $excel_results = $this->recCandidateRepository->prepareCandidatesExcel($results, $customerLoc);
        return $this->exportExcel($excel_results);
    }

    public function candidateTraining()
    {
        return view('recruitment::candidate-training');
    }

    public function candidateTrainingList()
    {
        $candidates=$this->recCandidateRepository->getCandidateTrainingList();
        return datatables()->of($candidates)->toJson();
    }

    public function compare($candidate_ids)
    {
        $ids = json_decode($candidate_ids);
        if (!empty($ids)) {
            $candidates = $this->recCandidateRepository->getCandidateComparisonList($ids);
        }
        $class_arr = ['Landed Immigrant' => 'report-red','Permanent Resident' => 'report-yellow','Canadian Citizen' => 'report-green',
                     'I have a full class G license' => 'report-green', 'I have a valid G1 license' => 'report-yellow','I have a valid G2 license' => 'report-yellow','Yes' => 'report-green','No' => 'report-normal',
                     '4 - Commissionaires is strategic to my long term career in security.' => 'report-green', '1 - Commissionaires is a temporary stop in my career. I have no long term plans.' => 'report-red', ];
        return view('recruitment::candidate-compare', compact('candidates', 'class_arr'));
    }
}
