<?php

namespace Modules\Hranalytics\Http\Controllers;

use View;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\HelperService;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\Languages;
use Illuminate\Support\Facades\Auth;
use Modules\Documents\Models\Document;
use Modules\Hranalytics\Models\Candidate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repositories\AttachmentRepository;
use Modules\Admin\Models\DocumentCategory;
use Modules\Admin\Models\DocumentNameDetail;
use Modules\Hranalytics\Models\CandidateJob;
use Modules\Admin\Models\JobPostFindingLookup;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Models\TrackingProcessLookup;
use Modules\Admin\Models\CandidateBrandAwareness;
use Modules\Hranalytics\Models\CandidateTracking;
use Modules\Hranalytics\Models\UseOfForceLookups;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Models\CandidateAttachmentLookup;
use Modules\Admin\Models\CandidateSecurityAwarenes;
use Modules\Hranalytics\Repositories\JobRepository;
use Modules\Hranalytics\Models\CandidateTermination;
use Modules\Hranalytics\Models\CandidateAvailability;
use Modules\Hranalytics\Models\CandidateJobInterview;
use Modules\Admin\Models\SecurityGuardLicenceThreshold;
use Modules\Documents\Repositories\DocumentsRepository;
use Modules\Admin\Repositories\FeedbackLookupRepository;
use Modules\Hranalytics\Http\Requests\HrTrackingRequest;
use Modules\Hranalytics\Repositories\CandidateRepository;
use Modules\UniformScheduling\Models\UniformMeasurements;
use Modules\Admin\Http\Requests\EmployeeConversionRequest;
use Modules\Admin\Models\CandidateScreeningQuestionLookup;
use Modules\Hranalytics\Models\CandidateScreeningQuestion;
use Modules\Hranalytics\Models\MyersBriggsPersonalityType;
use Modules\Admin\Models\UniformSchedulingMeasurementPoints;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Hranalytics\Models\CandidateTransitionAttachment;
use Modules\Admin\Repositories\JobPostFindingLookupRepository;
use Modules\Hranalytics\Http\Requests\CandidateInterviewRequest;
use Modules\Hranalytics\Models\CandidateScreeningOtherLanguages;
use Modules\Hranalytics\Http\Requests\CandidateTerminationRequest;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CommissionairesUnderstandingLookupRepository;

class CandidateController extends Controller
{

    protected $userRepository;
    protected $candidateRepository;
    protected $feedbackLookupRepository;
    protected $jobRepository;
    protected $customerRepository;
    protected $helperService;
    protected $attachmentRepository;
    protected $jobPostFindingLookupRepository;

    public function __construct(
        CandidateRepository $candidateRepository,
        FeedbackLookupRepository $feedbackLookupRepository,
        JobRepository $jobRepository,
        CustomerRepository $customerRepository,
        HelperService $helperService,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        AttachmentRepository $attachmentRepository,
        CommissionairesUnderstandingLookupRepository $commissionairesUnderstandingLookupRepository,
        DocumentsRepository $documentRepository,
        JobPostFindingLookupRepository $jobPostFindingLookupRepository
    ) {
        $this->candidateRepository = $candidateRepository;
        $this->feedbackLookupRepository = $feedbackLookupRepository;
        $this->jobRepository = $jobRepository;
        $this->customerRepository = $customerRepository;
        $this->userRepository = new UserRepository();
        $this->helperService = $helperService;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->commissionairesUnderstandingLookupRepository = $commissionairesUnderstandingLookupRepository;
        $this->employee_allocation = new EmployeeAllocationRepository();
        $this->documentRepository = $documentRepository;
        $this->jobPostFindingLookupRepository = $jobPostFindingLookupRepository;
    }

    /**
     * Candidate application summary - will list all candidates those who applied for the job
     *
     * @return void
     */
    public function index()
    {
        $feedbackLookups = $this->feedbackLookupRepository->getList();
        $tracking_users_arr = $this->candidateRepository->hrTrackingList(\Auth::user(), 'Proceed')->toArray();
        return view('hranalytics::candidate.screening-summary', compact('feedbackLookups', 'tracking_users_arr'));
    }

    /**
     *  Get the candidate list
     *
     * @param null
     * @return json
     */
    public function screeningSummaryList()
    {
        $candidates = $this->candidateRepository->getCandidates(
            $candidate_selection_status = null,
            $request = null,
            $type_of_records_request = null,
            $order_by = 'name',
            $customer_session = true
        );
        return datatables()->of($candidates)->toJson();
    }

    /**
     * Update Job Status
     *
     * @param Request $request
     * @return json
     */
    public function updateCandidateJobStatus(Request $request)
    {
        return $this->candidateRepository->updateJobStatus($request);
    }

    /**
     * To view the candidate application and candidate details
     *
     * @param [type] $candidate_id
     * @param [type] $job_id
     * @return void
     */
    public function viewCandidate($candidate_id, $job_id)
    {
        $candidateJob = $this->candidateRepository->getJobApplicationOfCandidate($candidate_id, $job_id);
        $availabilities = CandidateAvailability::where([['candidate_id', $candidateJob->candidate_id]])->first();
        $decoded_days = html_entity_decode($availabilities->days_required);
        $decoded_shifts = html_entity_decode($availabilities->shifts);
        $days_required = json_decode($decoded_days, true);
        $shifts = json_decode($decoded_shifts, true);
        $session_obj['job'] = $candidateJob->job;
        $brand_awareness_collection = CandidateBrandAwareness::orderby('order_sequence', 'asc')->pluck('answer', 'id')->toArray();
        $session_obj['brand_awareness'] = $brand_awareness_collection;
        $security_awareness_collection = CandidateSecurityAwarenes::orderby('order_sequence', 'asc')->get()->pluck('answer', 'id')->toArray();
        $session_obj['security_awareness'] = $security_awareness_collection;
        $job_post_finding_collection = isset($candidateJob->candidate->referalAvailibilit) ?
            $this->jobPostFindingLookupRepository
            ->getListWithSelected($candidateJob->candidate->referalAvailibility->job_post_finding) :
            [];
        $session_obj['job_post_finding'] = $job_post_finding_collection;
        Session::put('CANINFO', $session_obj);
        $lookups = $this->getLookups($job_id);
        $position_experience = json_decode($candidateJob->candidate->guardingexperience->positions_experinces, true);
        $event_log = $this->candidateRepository->getScheduleEventLogs($candidate_id);
        $otherlanguages = CandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)->get();
        $languages = Languages::get();
        $uniformdetails = [];
        $uniformdetails = UniformMeasurements::where("candidate_id", $candidate_id)
            ->get()
            ->pluck("measurement_values", "uniform_scheduling_measurement_point_id")
            ->toArray();
        //dd($uniformdetails);
        return view('hranalytics::candidate.view-candidate-application', compact(
            'candidateJob',
            'session_obj',
            'lookups',
            'position_experience',
            'event_log',
            'days_required',
            'shifts',
            'otherlanguages',
            'languages',
            'uniformdetails'
        ));
    }

    /*
     *Function for printview of candidate screening
     *
     * @param $id
     * @return view
     */
    public function printViewCandidateJob($id)
    {
        $candidateJob = CandidateJob::with([
            'candidate',
            'job',
            'candidate.uniform_measurements',
            'candidate.force.force_lookup',
            'candidate_brand_awareness',
            'candidate_security_awareness',
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
            'candidate.other_languages',
            'candidate.employment_history',
            'candidate.screening_questions',
            'candidate.skills',
            'candidate.comissionaires_understanding'
        ])
            ->find($id);
        //        dd($candidateJob->candidate->other_languages[0]->language_lookup);
        return view('hranalytics::candidate.print-application', compact('candidateJob'));
    }

    /**
     * Rate and review a candidate
     *
     * @param [type] $candidate_id
     * @param [type] $job_id
     * @param Request $request
     * @return void
     */
    public function reviewCandidate($candidate_id, $job_id)
    {
        $candidateJob = $this->candidateRepository->getJobApplicationOfCandidate($candidate_id, $job_id);

        $availabilities = CandidateAvailability::where([['candidate_id', $candidateJob->candidate_id]])->first();
        $decoded_days = html_entity_decode($availabilities->days_required);
        $decoded_shifts = html_entity_decode($availabilities->shifts);
        $days_required = json_decode($decoded_days, true);
        $shifts = json_decode($decoded_shifts, true);
        $interview_notes = $this->candidateRepository->getInterviewNotesOfCandidate($candidate_id, $job_id);
        $session_obj['job'] = $candidateJob->job;
        $brand_awareness_collection = CandidateBrandAwareness::orderby('order_sequence', 'asc')->pluck('answer', 'id')->toArray();
        $session_obj['brand_awareness'] = $brand_awareness_collection;
        $security_awareness_collection = CandidateSecurityAwarenes::orderby('order_sequence', 'asc')->get()->pluck('answer', 'id')->toArray();
        $session_obj['security_awareness'] = $security_awareness_collection;
        $job_post_finding_collection = JobPostFindingLookup::orderby('id', 'asc')->get()->pluck('job_post_finding', 'id')->toArray();
        $session_obj['job_post_finding'] = $job_post_finding_collection;
        Session::put('CANINFO', $session_obj);
        $lookups = $this->getLookups($job_id);
        $position_experience = json_decode($candidateJob->candidate->guardingexperience->positions_experinces, true);
        $interviewres = $this->userRepository->getUserLookup(null, ['admin', 'super_admin'], true, false, ['candidate-add-interview-notes']);
        $otherlanguages = CandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)->get();
        $languages = Languages::get();
        return view('hranalytics::candidate.review-application', compact(
            'candidateJob',
            'session_obj',
            'lookups',
            'position_experience',
            'interviewres',
            'interview_notes',
            'days_required',
            'shifts',
            'otherlanguages',
            'languages'
        ));
    }

    /*
     *Function to store Screening question Score
     *
     * @param Request $request
     * @return json
     */
    public function reviewScreeningAnswers(Request $request)
    {

        foreach ($request->get('score') as $question_id => $score) {
            CandidateScreeningQuestion::where(array('candidate_id' => $request->get('candidate_id'), 'question_id' => $question_id))->update(['score' => $score]);
        }
        $data['average_score'] = $request->get('total');
        $data['english_rating_id'] = $request->get('english_rating_id');
        $data['interview_score'] = $request->get('interview_score') ?? null;
        $data['interview_date'] = $request->get('interview_date') ?? null;
        $data['interview_notes'] = $request->get('interview_notes') ?? null;
        $data['reference_score'] = $request->get('reference_score') ?? null;
        $data['reference_date'] = $request->get('reference_date') ?? null;
        $data['reference_notes'] = $request->get('reference_notes') ?? null;
        //  dd($data);
        CandidateJob::updateOrCreate(array('candidate_id' => $request->get('candidate_id')), $data);
        return response()->json(array('success' => true));
    }

    /*
     *Function to store Interview Notes
     *
     * @param CandidateInterviewRequest $request
     * @return json
     */
    public function addInterviewNotes(CandidateInterviewRequest $request)
    {
        $session_obj = Session::get('CANINFO');
        $data['user_id'] = Auth::user()->id;
        $data['interviewer_id'] = $request->get('interviewer_id');
        $data['candidate_id'] = $request->get('candidate_id');
        $data['job_id'] = $request->get('job_id');
        $data['interview_date'] = $request->get('interview_date');
        $data['interview_notes'] = $request->get('interview_notes');
        CandidateJobInterview::updateOrCreate(array('candidate_id' => $request->get('candidate_id'), 'job_id' => $session_obj['job']->id), $data);
        return response()->json(array('success' => true));
    }

    /**
     * Selection of candidate - HR tracking process
     *
     * @param [type] $candidate_id
     * @param [type] $job_id
     * @return void
     */
    public function trackCandidate($candidate_id, $job_id)
    {
        $candidateJob = $this->candidateRepository->getJobApplicationOfCandidate($candidate_id, $job_id);
        $all_candidates = $this->candidateRepository->getCandidateAsList('Proceed');
        $all_jobs = $this->jobRepository->getJobsAsList(['approved', 'completed'])->toArray();
        $lookups = TrackingProcessLookup::orderBy('step_number', 'ASC')->get();
        $count_lookups = count($lookups);
        $users = $this->userRepository->getUserLookupByPermission(['hr-tracking']);
        $already_processed_track_ids = array();
        $trackings = CandidateTracking::where(['job_id' => $job_id, 'candidate_id' => $candidate_id])->whereHas('tracking_process')->get();
        $termination_reasons = \DB::table('candidate_termination_reason_lookups')->whereNull('deleted_at')->pluck('reason', 'id')->toArray();
        if (isset($trackings)) {
            foreach ($trackings as $each_track) {
                $already_processed_track_ids[$each_track->lookup_id] = $each_track;
            }
        }
        return view('hranalytics::candidate.hr-tracking', compact('lookups', 'users', 'candidateJob', 'all_candidates', 'all_jobs', 'already_processed_track_ids', 'count_lookups', 'termination_reasons'));
    }

    /**
     * Store tracking step
     *
     * @param [type] $candidate_id
     * @param [type] $job_id
     * @param Request $request
     * @return void
     */
    public function trackCandidateStore($candidate_id, $job_id, HrTrackingRequest $request)
    {
        return $this->candidateRepository->saveHrTrackingStep($candidate_id, $job_id, $request);
    }

    /**
     * Remove an HR tracking step
     *
     * @param [type] $job_id
     * @param [type] $step_id
     * @return void
     */
    public function hrTrackingRemove($job_id, $candidate_id, $step_id)
    {
        return $this->candidateRepository->deleteHrTracking($job_id, $candidate_id, $step_id);
    }

    /**
     * A list of candidates those are into selection process
     *
     * @param Request $request
     * @return boolean
     */
    public function candidateSummary(Request $request)
    {
        return view('hranalytics::candidate.hr-tracking-summary');
    }

    /*
     *Function to populate screening summary datatable
     *
     * @param null
     * @return json
     */
    public function candidateSummaryList()
    {
        $tracking_summary = $this->candidateRepository->getTrackingSummary();
        $tracking_summary_data = $this->candidateRepository->prepareTrackingSummaryRecords($tracking_summary);
        return datatables()->of($tracking_summary_data)->toJson();
    }

    /**
     * Showing Candidate mapping
     *
     * @param Request $request
     * @return view
     */
    public function mapping(Request $request)
    {
        return view('hranalytics::candidate.mapping');
    }

    /**
     * Showing client and candidates in google map
     *
     * @param [type] $job_id
     * @return void
     */
    public function plotJobCandidatesMap($job_id, Request $request)
    {
        $candidates = $this->candidateRepository->getCandidates('Proceed', $request);
        $job = $this->jobRepository->get($job_id);
        $lookups['client_name'] = $this->customerRepository->getList(PERMANENT_CUSTOMER);
        $personalityTypes = MyersBriggsPersonalityType::pluck('type', 'id')->toArray();
        $trackingProcess = TrackingProcessLookup::select(\DB::raw("CONCAT(step_number, ' - ',process_steps) as candidate_stage"), 'id')->pluck('candidate_stage', 'id')->toArray();
        return view('hranalytics::candidate.candidates-in-map', compact('candidates', 'job', 'lookups', 'request', 'personalityTypes', 'trackingProcess'));
    }

    /*
     *Function for Lookups
     *
     * @param $job_id
     * @return array
     */
    public function getLookups($job_id)
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
        $lookups['skills_lookup'] = \DB::table('skill_lookups')
            ->whereNull('deleted_at')
            ->get();
        $lookups['uniformcontrolLookups'] = UniformSchedulingMeasurementPoints::get();

        $lookups['languages_lookups'] = \DB::table('language_lookups')->whereNull('deleted_at')->get();
        $lookups['screening_questions'] = CandidateScreeningQuestionLookup::orderByRaw("FIELD(category , 'initiative','stress_tolerance','teamwork_interpersonal_group_dynamics','scenarios_problem_solving') ASC")->get();
        $lookups['attachmentLookups'] = CandidateAttachmentLookup::where('job_id', null)->orWhere('job_id', $job_id)->get();
        $lookups['division'] = \DB::table('division_lookups')->whereNull('deleted_at')->pluck('division_name', 'id')->toArray();
        $lookups['smart_phones'] = \DB::table('smart_phone_types')->whereNull('deleted_at')->pluck('type', 'id')->toArray();
        $lookups['experience_ratings'] = \DB::table('rate_experience_lookups')->whereNull('deleted_at')->orderby('score', 'desc')->pluck('experience_ratings', 'id')->toArray();
        $lookups['commissionaires_understanding'] = $this->commissionairesUnderstandingLookupRepository->getList();
        $lookups['english_ratings'] = \DB::table('english_rating_lookups')->whereNull('deleted_at')->orderby('order_sequence', 'asc')->pluck('english_ratings', 'id')->toArray();
        $lookups['threshold'] = SecurityGuardLicenceThreshold::pluck('threshold')->toArray();
        $lookups['force'] = UseOfForceLookups::orderby('order_sequence', 'asc')->get()->pluck('use_of_force', 'id')->toArray();

        return $lookups;
    }

    /*
     *Function to edit candidate screening details by super admin
     *
     * @param $candidate_id
     * @param $job_id
     * @return view
     */
    public function editCandidateJob($candidate_id, $job_id)
    {
        $lookups = $this->getLookups($job_id);
        $candidateJob = $this->candidateRepository->getJobApplicationOfCandidate($candidate_id, $job_id);
        $position_experience = json_decode($candidateJob->candidate->guardingexperience->positions_experinces, true);
        $availabilities = CandidateAvailability::where([['candidate_id', $candidateJob->candidate_id]])->first();
        $decoded_days = html_entity_decode($availabilities->days_required);
        $decoded_shifts = html_entity_decode($availabilities->shifts);
        $days_required = json_decode($decoded_days, true);
        $shifts = json_decode($decoded_shifts, true);
        $session_obj['job'] = $candidateJob->job;
        $languages = Languages::get();

        $session_obj['candidate'] = $candidateJob->candidate;
        $brand_awareness_collection = CandidateBrandAwareness::orderby('order_sequence', 'asc')->pluck('answer', 'id')->toArray();
        $session_obj['brand_awareness'] = $brand_awareness_collection;
        $security_awareness_collection = CandidateSecurityAwarenes::orderby('order_sequence', 'asc')->get()->pluck('answer', 'id')->toArray();
        $session_obj['security_awareness'] = $security_awareness_collection;
        $job_post_finding_collection = JobPostFindingLookup::orderby('id', 'asc')->get()->pluck('job_post_finding', 'id')->toArray();
        $session_obj['job_post_finding'] = $job_post_finding_collection;
        $attachement_ids = $candidateJob->candidate->attachements->pluck('attachment_file_name', 'attachment_id')->toArray();
        //$session_obj['brand_awareness'] = CandidateBrandAwareness::select('answer','id')->get();
        Session::put('CANINFO', $session_obj);
        $uniformdetails = UniformMeasurements::where("candidate_id", $candidate_id)
            ->get()
            ->pluck("measurement_values", "uniform_scheduling_measurement_point_id")
            ->toArray();
        $otherlanguages = CandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)->get();
        return view('hranalytics::candidate.edit-candidate-job', compact(
            'candidateJob',
            'session_obj',
            'lookups',
            'position_experience',
            'days_required',
            'shifts',
            'attachement_ids',
            'otherlanguages',
            'languages',
            'uniformdetails'
        ));
    }

    /*
     *Function to get details according to candidate selected in TrackingPage
     *
     * @param  Request $request
     * @return json
     */
    public function getCandidate($candidate_id)
    {
        $candidate = $this->candidateRepository->getCandidate($candidate_id);
        return response()->json($candidate);
    }

    /*
     *Function to get details according to job selected in TrackingPage
     *
     * @param  Request $request
     * @return json
     */
    public function getJob($job_id)
    {
        $job = $this->jobRepository->get($job_id);
        return response()->json($job);
    }

    /*
     *Function to download attachments
     *
     * @param  $file
     * @return
     */
    public function downloadFile($file)
    {
        return response()->download(public_path() . '/attachments/' . $file);
    }

    /**
     * Function to get Customer Details
     *
     * @param Request $request
     * @return json
     */
    public function getCustomer(Request $request)
    {
        $customer = Customer::select("client_name")
            ->where("id", $request->get('id'))
            ->first();
        return response()->json($customer);
    }

    /**
     * Archive Row
     *
     * @param Request $request
     * @return json
     */
    public function archive(Request $request)
    {
        return $this->candidateRepository->deleteCandidateJobs($request);
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
        $customer = Customer::pluck('project_number', 'id')->toArray();
        $documentCategoryDetails = DocumentCategory::where('document_category', 'Agreement')->first();
        $documentNameDetails = DocumentNameDetail::where('name', 'Signed Employee Contract')->first();
        $documentTypeID = EMPLOYEE;
        $banks = $page_variables_arr['banks'];
        $bank_code = $page_variables_arr['bank_code'];
        $payment_methods = $page_variables_arr['payment_methods'];
        $marital_status = $page_variables_arr['marital_status'];
        $salutation = $page_variables_arr['salutation'];
        $payroll_group = $page_variables_arr['payroll_group'];
        $relation = $page_variables_arr['relation'];
        $customers = $page_variables_arr['customers'];

        $approversList = $this->userRepository->getUserList(true, null, null, ['super_admin'])->sortBy('full_name')->pluck('full_name', 'id')->toArray();
        return view(
            'hranalytics::candidate-conversion.conversion-list',
            compact(
                'roles',
                'employees',
                'work_types',
                'security_clearances',
                'positions',
                'customer',
                'date',
                'certificates',
                'approversList',
                'documentCategoryDetails',
                'documentNameDetails',
                'documentTypeID',
                'bank_code',
                'banks',
                'payment_methods',
                'payroll_group',
                'customers',
                'marital_status',
                'relation'
            )
        );
    }

    /*
     *Function to populate  candidates those are in last tracking step listed
     *
     * @param null
     * @return json
     */
    public function candidateConversionList()
    {
        $tracking_summary = $this->candidateRepository->getCandidateConversionList();
        return datatables()->of($tracking_summary)->addIndexColumn()->toJson();
    }

    public function showDetails($id)
    {
        $user_details = $this->candidateRepository->getCandidateDetails($id);
        if (is_object($user_details)) {
            if (is_numeric($user_details->candidate->guardingExperience->years_security_experience)) {
                $employee_data['years_of_security'] = (int)$user_details->candidate->guardingExperience->years_security_experience;
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function Employeestore(EmployeeConversionRequest $request, $module)
    {
        try {
            \DB::beginTransaction();
            $user_store = $this->userRepository->userStore($request);
            $candidate_id = $request->get('candidate_id');
            $mapping_id = $this->candidateRepository->candidateEmployeeMapping($user_store, $candidate_id);
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
            $transition_attachment['candidate_transition_id'] = $mapping_id;
            $transition_attachment['attachment_id'] = $file['file_id'];
            CandidateTransitionAttachment::create($transition_attachment);
            $transation_documet = [
                'user_id' => $user_store->id,
                'document_type_id' => $request->document_type_id,
                'document_category_id' => $request->document_category_id,
                'document_name_id' => $request->document_name_id,
            ];
            $transation_documet['answer_type'] = $this->documentRepository->getCategoryModels(null, $request->document_category_id);
            $transation_documet['attachment_id'] = $file['file_id'];
            $transation_documet['created_by'] = Auth::id();
            Document::create($transation_documet);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            \DB::rollback();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Terminate candidate
     *
     * @param Request $request
     * @param [type] $candidate_id
     * @return void
     */
    public function terminateCandidateApplication(CandidateTerminationRequest $request, $candidate_id)
    {
        $data['candidate_id'] = $candidate_id;
        $data['reason_id'] = $request->get('reason_id');
        $data['reason'] = $request->get('reason');
        $data['user_id'] = $user = \Auth::user()->id;
        CandidateTermination::create($data);
        return response()->json($this->helperService->returnTrueResponse());
    }

    /**
     * Reactivate candidate Application
     *
     * @param Request $request
     * @param [type] $candidate_id
     * @param boolean $reset_all
     * @return void
     */
    public function reactivateCandidateApplication(Request $request, $candidate_id, $reset_all = false)
    {
        $data['candidate_id'] = $candidate_id;
        CandidateTermination::where('candidate_id', $candidate_id)->delete();
        if ($reset_all) {
            CandidateTracking::where('candidate_id', $candidate_id)->delete();
        }
        return response()->json($this->helperService->returnTrueResponse());
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
        return $this->candidateRepository->deleteCandidateAttachment($candidate_id, $attachment_id);
    }

    /**
     * Candidate Excel Export
     * @param  $request
     * @return response
     */
    public function candidateExport(Request $request)
    {
        $results = $this->candidateRepository->getCandidates();
        $excel_results = $this->candidateRepository->prepareCandidatesExcel($results);
        return $this->exportExcel($excel_results);
    }


    public function candidateGeomappingExport()
    {
        session_start();
        $candidate_arr = isset($_SESSION['geomapping_export_candidate_arr']) ? $_SESSION['geomapping_export_candidate_arr'] : null;
        $customerId = isset($_SESSION['geomapping_export_customerId']) ? $_SESSION['geomapping_export_customerId'] : null;
        $customerLoc = Customer::select('geo_location_lat', 'geo_location_long')->where('id', $customerId)->first();
        $results = $this->candidateRepository->getCandidatesById($candidate_arr);
        $excel_results = $this->candidateRepository->prepareCandidatesExcel($results, $customerLoc);
        return $this->exportExcel($excel_results);
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

    /*
     *Function to download attachments
     *
     * @param  $file
     * @return
     */
    public function downloadTestScoreDocument($file_id)
    {
        try {
            $path = $this->candidateRepository->testScoreAttachment($file_id);
            return response()->download($path['path'], $path['file'], []);
        } catch (\Exception $e) {
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
        }
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
            $path = $this->candidateRepository->forceAttachment($file_id);
            return response()->download($path['path'], $path['file'], []);
        } catch (\Exception $e) {
            return response()->json(array('success' => 'false', 'error' => $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile()));
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
            $candidate = Candidate::find($candidate_id);
            if (!empty($candidate)) {
                $candidateImageName = $this->userRepository->uploadProfileImage($request, $candidate_id, 'candidate_profile');
                if ($candidateImageName != null) {
                    $insertCandidate = $candidate->toArray();
                    $insertCandidate['profile_image'] = $candidateImageName;
                    Candidate::updateOrCreate(['id' => $candidate_id], $insertCandidate);
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
}
