<?php

namespace Modules\Hranalytics\Http\Controllers;

use Carbon;
use Config;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Log;
use Mail;
use Modules\Admin\Models\CandidateAttachmentLookup;
use Modules\Admin\Models\CandidateBrandAwareness;
use Modules\Admin\Models\CandidateScreeningQuestionLookup;
use Modules\Admin\Models\Languages;
use App\Repositories\AttachmentRepository;
use Modules\Admin\Models\JobPostFindingLookup;
use Modules\Hranalytics\Models\CandidateReferalAvailability;
use Modules\Admin\Models\CandidateSecurityAwarenes;
use Modules\Admin\Models\UniformSchedulingMeasurementPoints;
use Modules\Admin\Repositories\CommissionairesUnderstandingLookupRepository;
use Modules\Admin\Repositories\CompetencyMatrixLookupRepository;
use Modules\Admin\Repositories\CompetencyMatrixRatingLookupRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Hranalytics\Http\Requests\AttachmentRequest;
use Modules\Hranalytics\Http\Requests\CandidateRequest;
use Modules\Hranalytics\Http\Requests\ScreeningQuestionsRequest;
use Modules\Hranalytics\Http\Requests\UniformEntryRequest;
use Modules\Hranalytics\Mail\CandidateNotification;
use Modules\Hranalytics\Models\Candidate;
use Modules\Hranalytics\Models\CandidateScreeningOtherLanguages;
use Modules\Admin\Models\SecurityGuardLicenceThreshold;
use Modules\Hranalytics\Models\CandidateAddress;
use Modules\Hranalytics\Models\CandidateAttachment;
use Modules\Hranalytics\Models\CandidateAvailability;
use Modules\Hranalytics\Models\CandidateCommissionairesUnderstanding;
use Modules\Hranalytics\Models\CandidateEducation;
use Modules\Hranalytics\Models\CandidateEmploymentHistory;
use Modules\Hranalytics\Models\CandidateExperience;
use Modules\Hranalytics\Models\CandidateForceCertification;
use Modules\Hranalytics\Models\CandidateJob;
use Modules\Hranalytics\Models\CandidateLanguage;
use Modules\Hranalytics\Models\CandidateMiscellaneouses;
use Modules\Hranalytics\Models\CandidateReference;
use Modules\Hranalytics\Models\CandidateScreeningPersonalityTestQuestion;
use Modules\Hranalytics\Models\CandidateScreeningQuestion;
use Modules\Hranalytics\Models\CandidateSecurityClearance;
use Modules\Hranalytics\Models\CandidateSecurityGuardingExperince;
use Modules\Hranalytics\Models\CandidateSecurityProximity;
use Modules\Hranalytics\Models\CandidateSettings;
use Modules\Hranalytics\Models\CandidateSkills;
use Modules\Hranalytics\Models\CandidateWageExpectation;
use Modules\Hranalytics\Models\Job;
use Modules\Hranalytics\Models\UseOfForceLookups;
use Modules\Hranalytics\Repositories\CandidateScreeningCompetencyMatrixRepository;
use Modules\Hranalytics\Repositories\CandidateScreeningPersonalityInventoryRepository;
use Modules\Hranalytics\Repositories\CandidateScreeningPersonalityScoreRepository;
use Modules\UniformScheduling\Repositories\UniformMeasurementsRepository;
use PDF;
use Session;

class JobApplicationController extends Controller
{

    private $directory_seperator;
    private $extension_seperator;
    protected $userRepository;
    protected $uniformMeasurementsRepository;
    /**
     * Create a new  instance.
     *
     *
     */
    public function __construct(
        CompetencyMatrixLookupRepository $competencyMatrixLookupRepository,
        CompetencyMatrixRatingLookupRepository $competencyMatrixRatingLookupRepository,
        CandidateScreeningCompetencyMatrixRepository $candidateScreeningCompetencyMatrixRepository,
        CommissionairesUnderstandingLookupRepository $commissionairesUnderstandingLookupRepository,
        AttachmentRepository $attachmentRepository,
        UserRepository $userRepository,
        UniformMeasurementsRepository $uniformMeasurementsRepository
    ) {
        $this->personality_score_repository = new CandidateScreeningPersonalityScoreRepository();
        $this->personality_inventory_repository = new CandidateScreeningPersonalityInventoryRepository();
        $this->competency_matrix_lookup_repository = $competencyMatrixLookupRepository;
        $this->competency_matrix_rating_lookup_repository = $competencyMatrixRatingLookupRepository;
        $this->candidate_screening_competency_matrix_repository = $candidateScreeningCompetencyMatrixRepository;
        $this->commissionaires_understanding_lookup_repository = $commissionairesUnderstandingLookupRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->userRepository = $userRepository;
        $this->uniformMeasurementsRepository = $uniformMeasurementsRepository;
        $this->directory_seperator = "/";
        $this->extension_seperator = ".";
    }

    /**
     * Login to the System
     *
     * @param  Illuminate\Http\Request;     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if ($request->get('type') == 'normal') {
            $job_id = trim($request->get('job_id'));
            $password = $request->get('g_password');
            $job = Job::where(['unique_key' => $job_id, 'status' => 'approved'])->first();
            $pass_flag = CandidateSettings::where(['generic_password' => $password])->get()->count();
            $login = isset($job) && $pass_flag;
            $session_obj['job'] = $job;
            $session_obj['type'] = 'normal';
            $session_obj['url'] = route('applyjob');
        } else {
            //$name = strtolower(trim($request->get('name')));
            $email = strtolower(trim($request->get('email')));
            //need to check application status here
            $candidate = Candidate::where(function ($q2) use ( /* $name, */$email) {
                //$q2->whereRaw('LOWER(`name`) like ?', array($name));
                $q2->WhereRaw('LOWER(`email`) like ?', array($email));
            })->first();

            $login = isset($candidate);
            $session_obj['candidate'] = $candidate;
            $session_obj['type'] = 'recurring';
            $session_obj['url'] = route('applyjob.dashboard');
        }
        if ($login) {
            $request->session()->put('CANINFO', $session_obj);

            return response()->json(array('success' => true, 'url' => $session_obj['url']));
        } else {
            return response()->json(array('success' => false));
        }
    }

    /**
     * Get Lookup values for the dropdowns in Candidate Screening
     *
     * @return Array
     */
    public function getLookups()
    {

        $session_obj = Session::get('CANINFO');
        $lookups['positions_lookups'] = DB::table('position_lookups')->whereNull('deleted_at')->orderby('position', 'asc')->pluck('position', 'id')->toArray();
        $lookups['security_provider'] = DB::table('security_provider_lookups')->whereNull('deleted_at')->pluck('security_provider', 'id')->toArray();
        //$lookups['previous_roles'] = DB::table('position_lookups')->whereNull('deleted_at')->pluck('position', 'id')->toArray();
        $lookups['experience_ratings'] = DB::table('rate_experience_lookups')->whereNull('deleted_at')->orderby('score', 'desc')->pluck('experience_ratings', 'id')->toArray();
        $lookups['skills_lookup'] = DB::table('skill_lookups')->whereNull('deleted_at')->get();
        $lookups['languages_lookups'] = DB::table('language_lookups')->whereNull('deleted_at')->get();
        $lookups['screening_questions'] = CandidateScreeningQuestionLookup::orderByRaw("FIELD(category , 'initiative','stress_tolerance','teamwork_interpersonal_group_dynamics','scenarios_problem_solving') ASC")->get();
        $lookups['attachmentLookups'] = CandidateAttachmentLookup::where('job_id', null)->orWhere('job_id', $session_obj['job']->id)->get();
        $lookups['division'] = DB::table('division_lookups')->whereNull('deleted_at')->pluck('division_name', 'id')->toArray();
        $lookups['smart_phones'] = DB::table('smart_phone_types')->whereNull('deleted_at')->pluck('type', 'id')->toArray();
        $lookups['personality_questions'] = CandidateScreeningPersonalityTestQuestion::with(['options'])->get();
        $lookups['competency_matrix'] = $this->competency_matrix_lookup_repository->getCompetency();
        $lookups['competency_rating'] = $this->competency_matrix_rating_lookup_repository->getList();
        $lookups['commissionaires_understanding'] = $this->commissionaires_understanding_lookup_repository->getList();
        $lookups['threshold'] = SecurityGuardLicenceThreshold::pluck('threshold')->toArray();
        return $lookups;
    }

    /**
     * Apply new Job Login Screen
     *
     * @param  Illuminate\Http\Request;     *
     * @return view
     */

    public function applyjob(Request $request)
    {
        //  $candidateJob = CandidateJob::with('candidate', 'candidate.addresses', 'candidate.availability', 'candidate.securityclearance', 'candidate.guardingexperience', 'candidate.securityproximity', 'candidate.wageexpectation', 'candidate.experience', 'candidate.miscellaneous', 'candidate.employment_history', 'candidate.references', 'candidate.educations', 'candidate.languages', 'candidate.screening_questions', 'candidate.skills')
        //    ->where('candidate_id', "=", 3)
        //     ->where('job_id', "=", 48)
        //  ->first();
        // return view('hranalytics::job-application.application', compact('candidateJob'));
        $session_obj = $request->session()->get('CANINFO');
        if (isset($session_obj) && $session_obj != null && isset($session_obj['type'])) {
            if (null !== $request->get('job_id')) {
                $job = Job::where(['unique_key' => $request->get('job_id'), 'status' => 'approved'])->first();
                $session_obj['job'] = $job;
                $request->session()->put('CANINFO', $session_obj);
            }
            if (isset($session_obj['job'])) {
                if (Job::where(['id' => $session_obj['job']->id, 'status' => 'approved'])->count() > 0) {
                    $lookups = $this->getLookups();
                    $brand_awareness_collection = CandidateBrandAwareness::orderby('order_sequence', 'asc')->pluck('answer', 'id')->toArray();
                    $session_obj['brand_awareness'] = $brand_awareness_collection;
                    $security_awareness_collection = CandidateSecurityAwarenes::orderby('order_sequence', 'asc')->get()->pluck('answer', 'id')->toArray();
                    $session_obj['security_awareness'] = $security_awareness_collection;
                    $job_post_finding_collection = JobPostFindingLookup::orderby('id', 'asc')->get()->pluck('job_post_finding', 'id')->toArray();
                    $session_obj['job_post_finding'] = $job_post_finding_collection;
                    $otherlanguages = collect([]);
                    $languages = Languages::get();
                    $lookups['uniformcontrolLookups'] = UniformSchedulingMeasurementPoints::get();
                    $lookups['force'] = UseOfForceLookups::orderby('order_sequence', 'asc')->get()->pluck('use_of_force', 'id')->toArray();
                    return view('hranalytics::job-application.applyjob', compact(
                        'session_obj',
                        'lookups',
                        'otherlanguages',
                        'languages',
                        'force'
                    ));
                } /* else {
                $session_obj = null;
                $request->session()->put('CANINFO', $session_obj);
                return redirect(route('applyjob.dashboard'));
                } */
            } /* else {
            return redirect(route('applyjob.dashboard'));
            } */
        } else {
            $session_obj = null;
            $request->session()->put('CANINFO', $session_obj);
        }
        $lookups['attachmentLookups'] = CandidateAttachmentLookup::get();

        return view('hranalytics::job-application.login', compact('lookups'));
    }

    /**
     * Apply new Job Login Screen
     *
     * @param  Illuminate\Http\Request;     *
     * @return view
     */
    public function previousapplication(Request $request)
    {
        $request->get('job_id');
        $session_obj = $request->session()->get('CANINFO');
        $job = Job::where(['unique_key' => $request->get('job_id'), 'status' => 'approved'])->first();
        $session_obj['job'] = $job;
        $request->session()->put('CANINFO', $session_obj);
        $lookups = $this->getLookups();
        $candidateJob = CandidateJob::with('candidate', 'candidate.addresses', 'candidate.availability', 'candidate.securityclearance', 'candidate.guardingexperience', 'candidate.securityproximity', 'candidate.wageexpectation', 'candidate.experience', 'candidate.miscellaneous', 'candidate.employment_history', 'candidate.references', 'candidate.educations', 'candidate.languages', 'candidate.screening_questions', 'candidate.skills', 'candidate.comissionaires_understanding')
            ->where('candidate_id', "=", $session_obj['candidate']->id)
            ->first();

        $position_experience = json_decode($candidateJob->candidate->guardingexperience->positions_experinces, true);
        return view('front.job-application.applyjob', compact('candidateJob', 'session_obj', 'lookups', 'position_experience'));
    }

    /**
     * Candidate Screening Dashboard
     *
     * @param  Illuminate\Http\Request;     *
     * @return view
     */
    public function dashboard(Request $request)
    {
        $session_obj = $request->session()->get('CANINFO');
        if (isset($session_obj) && $session_obj != null && isset($session_obj['candidate'])) {
            return view('front.job-application.dashboard');
        } else {
            $session_obj = null;
            $request->session()->put('CANINFO', $session_obj);
            return redirect(route('applyjob'));
        }
    }

    /**
     * Get list of jobs
     *
     *@param  Illuminate\Http\Request;     *
     * @return datatable object
     */
    public function getJobList(Request $request)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $session_obj = $request->session()->get('CANINFO');
        //$applied_job_ids = CandidateJob::where(['candidate_id' => $session_obj['candidate']->id])->select('job_id')->get()->toArray();
        $jobs = Job::where(['status' => 'approved'])->with([
            'candidate_jobs' => function ($query) use ($session_obj) {
                $query->where('candidate_id', '=', $session_obj['candidate']->id);
            },
            'positionBeeingHired',
            'customer',
            'assignment_type',
        ])->select([
            DB::raw('@rownum  := @rownum  + 1 AS rownum'), 'id', 'unique_key', 'customer_id', 'open_position_id', 'no_of_vaccancies', 'job_description', 'city', 'assignment_type_id', 'requisition_date', 'wage_low', 'wage_high', 'status', 'active',
        ])->orderby('id', 'DESC')->get();
        return datatables()->of($jobs)->toJson();
    }
    /**
     * Get Other Languages
     *
     *@param  Illuminate\Http\Request;     *
     * @return datatable object
     */
    public function getOtherlanguages(Request $request)
    {
        $languages = Languages::get();
        $id = $request->id;
        return view('hranalytics::job-application.partials.profile.language', compact('languages', 'id'));
    }
    /**
     * Logout from the System
     *
     *  @param  Illuminate\Http\Request;
     * @return view
     */
    public function logout(Request $request)
    {
        $request->session()->put('CANINFO', null);
        return redirect(route('applyjob'));
    }

    /**
     * PDF view of Application
     *
     *@param  Illuminate\Http\Request;
     *@param  $id
     * @return view
     */
    public function viewApplication($id, Request $request)
    {
        $session_obj = $request->session()->get('CANINFO');
        if (isset($session_obj) && isset($session_obj['candidate']) && null != $session_obj['candidate']) {
            $candidateJob = CandidateJob::with(
                'candidate',
                'job',
                'candidate.addresses',
                'candidate.availability',
                'candidate.securityclearance',
                'candidate.guardingexperience',
                'candidate.force.force_lookup',
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
                'candidate.interviewnote',
                'candidate.comissionaires_understanding',
                'candidate.personality_scores.score_type',
                'candidate.competency_matrix.competency_matrix'
            )
//                                ->where(['candidate_id' => 695, 'job_id' => 171])
                ->where(['candidate_id' => $session_obj['candidate']->id, 'job_id' => $id])
                ->first();
            //
            $candidateJob->status = 'Applied';
            $candidateJob->submitted_date = Carbon::now();
            $candidateJob->save();
            $session_obj = null;
            $request->session()->put('CANINFO', $session_obj);

            try {
                $candidate_application_filename = $this->pdfGenerate($candidateJob);
                $this->sendNotification($candidateJob->candidate_id, $candidate_application_filename);
            } catch (\Exception $e) {
                \Log::error("Candidate pdf/email error : ".$e->getMessage().' at '.$e->getLine().' in '.$e->getFile());
            }

//            return view('hranalytics::job-application.application', compact('candidateJob'));
            return view('hranalytics::job-application.partials.view_pdf', compact('candidateJob'));
        } else {
            $session_obj = null;
            $request->session()->put('CANINFO', $session_obj);
            return redirect(route('applyjob'));
        }
    }

    /**
     * PDF download of Application
     *
     *@param  $id
     * @return view
     */
    public function downloadPDF($id)
    {
        $candidateJob = CandidateJob::find($id);
        return view('front.job-application.partials.view_pdf', compact('candidateJob'));
        /* $dompdf = new Dompdf();
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $html = view('front.job-application.pdf', compact('candidateJob'));
        $folder_path = array("../../images", "../../css");
        $public_path = array(public_path() . "/images", public_path() . "/css");
        $html = str_replace($folder_path, $public_path, $html);
        $html .= '<style type="text/css">.pdf-hide { display: none; } .experience-year{ margin-left: 200px; } .mw{ max-width: 10.666667%; } .mwl{     max-width: 28.5%; } .hour{ max-width: 20%;}  .pdfgen-label,.pdf-row,.pdfgen-label,.pdfgen-display{
        float: left;
        }
        .pdf-row{
        width: 100%!important;
        display: block!important;
        }
        .pdfgen-label{
        width: 40%!important;
        max-width: 100%!important;
        }
        .pdfgen-display{
        width: 60%!important;
        max-width: 100%!important;
        }
        .pdf-border{
        width: 100%!important;
        }</style>';
        $dompdf->loadHTML($html);
        $dompdf->setOptions($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->output();
        $dompdf->stream("Candidate_Application.pdf", array("Attachment" => 1)); */
    }

    /**
     * Function to check Candidate is already applied or not
     *@param $email
     *@param $phone_home
     *@param $phone_cellular
     *@param $job_id
     * @return \Illuminate\Http\Response
     */
    public function isCandidateAlreadyApplied($email, $phone_home, $phone_cellular, $job_id)
    {

        $candidate = Candidate::where(['email' => $email])->first();
        /*if ($candidate == null && !empty($phone_home)) {
        $candidate = Candidate::where(['phone_home' => $phone_home])->first();
        }
        if ($candidate == null && !empty($phone_cellular)) {
        $candidate = Candidate::where(['phone_cellular' => $phone_cellular])->first();
        }*/
        $candidate_id = ($candidate != null) ? $candidate->id : 0;

        return (CandidateJob::where('candidate_id', '=', $candidate_id)

            // ->orwhere('job_id', '=', $job_id)
            ->where('status', "=", "Applied")
            ->count());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CandidateRequest $request)
    {
        $session_obj = $request->session()->get('CANINFO');

        // try {
        DB::beginTransaction();
        $already_applied = $this->isCandidateAlreadyApplied($request->get('email'), $request->get('phone_home'), $request->get('phone_cellular'), $session_obj['job']->id);

        if ($request->get('mode') != 'edit' && $already_applied > 0) {
            //$session_obj = null;
            //$request->session()->put('CANINFO', $session_obj);
            return response()->json(array('success' => false, 'job_applied' => true));
        }
        $insertCandidate = array(
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'name' => $request->get('first_name') . ' ' . $request->get('last_name'),
            'email' => $request->get('email'),
            'dob' => $request->get('dob'),
            'address' => $request->get('address'),
            'city' => $request->get('city'),
            'postal_code' => $request->get('postal_code'),
            'phone_home' => $request->get('phone_home'),
            'phone_cellular' => $request->get('phone_cellular'),
            'smart_phone_type_id' => $request->get('smart_phone_type_id'),
            'smart_phone_skill_level' => $request->get('smart_phone_skill_level'),
            'profile_image' => null,
        );
        $candidate = Candidate::where(['email' => $request->get('email')])->first();
        if ($candidate === null) {
            $candidate = Candidate::create($insertCandidate);
        } else {
            $insertCandidate['profile_image'] = $candidate->profile_image;
            $candidate = Candidate::updateOrCreate(['id' => (int) $candidate->id], $insertCandidate);
        }
        $session_obj['candidate'] = $candidate;
        $request->session()->put('CANINFO', $session_obj);
        $candidate_id = $session_obj['candidate']->id;


        //candidate image upload
        $candidateImageName = $this->userRepository->uploadProfileImage($request, $candidate_id, 'candidate_profile');
        if ($candidateImageName != null) {
            $insertCandidate['profile_image'] = $candidateImageName;
        }

        Candidate::updateOrCreate(['id' => (int) $candidate_id], $insertCandidate);
        $this->storeJob($request, $candidate_id);
        if (null !== $request->get('prev_address')) {
            $this->storeAddress($request, $candidate_id);
        }
        $this->storeCandidateReferalAvailability($request, $candidate_id);
        $this->storeCandidateCommissionairesUnderstanding($request, $candidate_id);
        $this->storeSecurityExperience($request, $candidate_id);
        $this->storeCandidateForceCertification($request, $candidate_id);
        $this->storeWageExpectation($request, $candidate_id);
        $this->storeAvailability($request, $candidate_id);
        $this->storeSecurityClearance($request, $candidate_id);
        $this->storeSecurityProximity($request, $candidate_id);
        $this->saveEmployementHistory($request, $candidate_id);
        $this->storeReference($request, $candidate_id);
        $this->storeEducation($request, $candidate_id);
        $this->storeExperiences($request, $candidate_id);
        $this->storeMiscellaneous($request, $candidate_id);
        $this->storeLanguages($request, $candidate_id);
        $this->storeSkills($request, $candidate_id);
        $this->storeOtherlanguage($request, $candidate_id);
        DB::commit();
        return response()->json(array('success' => true));
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     $content['success'] = false;
        //     $content['message'] = $e->getMessage() . ' ' . $e->getLine();
        //     $content['code'] = 406;
        //     Log::error('CandidateError ' . $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getTraceAsString());
        //     return response()->json($content);
        // }
    }
    public function storeCandidateReferalAvailability($request, $candidate_id)
    {
        $candidateavailability = new CandidateReferalAvailability;
        $candidateavailability->candidate_id = $candidate_id;
        $candidateavailability->orientation = $request->get('orientation');
        $candidateavailability->job_post_finding = $request->get('job_post_finding');
        $candidateavailability->sponser_email = $request->get('sponser_email');
        $candidateavailability->position_availibility = $request->get('position_availibility');
        $candidateavailability->floater_hours = $request->get('floater_hours');
        $candidateavailability->starting_time = $request->get('starting_time');
        $candidate = Candidate::find($candidate_id);
        $candidate->referalAvailibility()->delete();
        $candidate->referalAvailibility()->save($candidateavailability);
    }
    /**
     * Store Candidate Job Details
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */

    public function storeJob($request, $candidate_id)
    {
        $session_obj = $request->session()->get('CANINFO');
        $data['candidate_id'] = $candidate_id;
        $data['job_id'] = $session_obj['job']->id;
        $data['fit_assessment_why_apply_for_this_job'] = $request->get('fit_assessment_why_apply_for_this_job');
        $data['brand_awareness_id'] = $request->get('brand_awareness_id');
        $data['security_awareness_id'] = $request->get('security_awareness_id');
        CandidateJob::updateOrCreate(array('candidate_id' => $candidate_id, 'job_id' => $session_obj['job']->id), $data);
    }

    /**
     * Store Candidate Commissionaires Understanding
     * @param  $Understandings, $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeCandidateCommissionairesUnderstanding($request, $candidate_id)
    {
        $understanding_ids = $request->get('candidate_commissionaires_understandings_id');
        CandidateCommissionairesUnderstanding::where(['candidate_id' => $candidate_id])->delete();
        foreach ($understanding_ids as $value) {
            $data['candidate_id'] = $candidate_id;
            $data['commissionaires_understanding_lookups_id'] = $value;
            CandidateCommissionairesUnderstanding::create($data);
        }
    }

    /**
     * Store Candidate Address
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeAddress($request, $candidate_id)
    {
        $prev_address = $request->get('prev_address');
        $prev_address_from = $request->get('prev_address_from');
        $prev_address_to = $request->get('prev_address_to');
        foreach ($prev_address as $key => $value) {
            $data[$key]['address'] = $prev_address[$key];
            $data[$key]['from'] = $prev_address_from[$key];
            $data[$key]['to'] = $prev_address_to[$key];
            $data[$key]['candidate_id'] = $candidate_id;
        }
        CandidateAddress::where(['candidate_id' => $candidate_id])->delete();
        CandidateAddress::insert($data);
    }

    /**
     * Store Candidate Guarding Experiences
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeSecurityExperience($request, $candidate_id)
    {
        $candidateguardingexperience = new CandidateSecurityGuardingExperince;
        $candidateguardingexperience->candidate_id = $candidate_id;
        $candidateguardingexperience->guard_licence = $request->get('guard_licence');
        $candidateguardingexperience->start_date_guard_license = $request->get('start_date_guard_license');
        $candidateguardingexperience->start_date_first_aid = $request->get('start_date_first_aid');
        $candidateguardingexperience->start_date_cpr = $request->get('start_date_cpr');
        $candidateguardingexperience->expiry_guard_license = $request->get('expiry_guard_license');
        $candidateguardingexperience->expiry_first_aid = $request->get('expiry_first_aid');
        $candidateguardingexperience->expiry_cpr = $request->get('expiry_cpr');
        $candidateguardingexperience->test_score_percentage = null !== ($request->get('test_score_percentage')) ? $request->get('test_score_percentage') : null;
        $document_attachments = $request->test_score_document_id;
        $request->merge(['candidate_id' => $candidate_id]);
        if (!empty($document_attachments)) {
            $file = $this->attachmentRepository->saveAttachmentFile('candidate-recruitment', $request, 'test_score_document_id');
            $attachment_id = $file['file_id'];
            $candidateguardingexperience->test_score_document_id = $attachment_id;
        }

        if (isset($request->test_score_doc_id)) {
            $candidateguardingexperience->test_score_document_id = $request->test_score_doc_id;
        }
        if ($candidateguardingexperience->guard_licence == 'Yes') {
            $candidateguardingexperience->security_clearance = $request->get('security_clearance');
        } else {
            $candidateguardingexperience->security_clearance = null;
        }
        if ($candidateguardingexperience->security_clearance == 'Yes') {
            $candidateguardingexperience->security_clearance_type = $request->get('security_clearance_type');
            $candidateguardingexperience->security_clearance_expiry_date = $request->get('security_clearance_expiry_date');
        } else {
            $candidateguardingexperience->sin_expiry_date_status = null;
            $candidateguardingexperience->sin_expiry_date = null;
        }
        $candidateguardingexperience->social_insurance_number = $request->get('social_insurance_number');

        if ($candidateguardingexperience->social_insurance_number == 1) {
            $candidateguardingexperience->sin_expiry_date_status = $request->get('sin_expiry_date_status') == '1' ? 1 : 0;
            if ($candidateguardingexperience->sin_expiry_date_status == 1) {
                $candidateguardingexperience->sin_expiry_date = $request->get('sin_expiry_date');
            } else {
                $candidateguardingexperience->sin_expiry_date = null;
            }
        } else {
            $candidateguardingexperience->sin_expiry_date_status = null;
            $candidateguardingexperience->sin_expiry_date = null;
        }

        $candidateguardingexperience->years_security_experience = $request->get('years_security_experience');
        $candidateguardingexperience->most_senior_position_held = $request->get('most_senior_position_held');

        /* positions held in past */
        $positions_lookups = DB::table('position_lookups')->whereNull('deleted_at')->orderBy('position', 'asc')->pluck('position', 'id')->toArray();
        foreach ($positions_lookups + array(0 => 'Other') as $each_position) {
            $control_name = str_replace(' ', '_', strtolower($each_position));
            $array_position[$control_name] = $request->get($control_name);
        }
        //$array_position = array('site_supervisor' => $request->get('site_supervisor'), 'shift_leader' => $request->get('shift_leader'), 'foot_patrol' => $request->get('foot_patrol'), 'concierge' => $request->get('concierge'), 'security_guard' => $request->get('security_guard'), 'access_control' => $request->get('access_control'), 'cctv_operator' => $request->get('cctv_operator'), 'mobile_patrols' => $request->get('mobile_patrols'), 'investigations' => $request->get('investigations'), 'loss_prevention_officer' => $request->get('loss_prevention_officer'), 'operations' => $request->get('operations'), 'dispatch' => $request->get('dispatch'), 'other' => $request->get('other'));
        $array_position[$control_name] = $request->get('other');
        $candidateguardingexperience->positions_experinces = json_encode($array_position);

        $candidate = Candidate::find($candidate_id);
        $candidate->guardingexperience()->delete();
        $candidate->guardingexperience()->save($candidateguardingexperience);
    }

    /**
     * Store candidate force certification
     */
    public function storeCandidateForceCertification($request, $candidate_id)
    {
        $candidateForce = new CandidateForceCertification;
        $candidateForce->candidate_id = $candidate_id;
        $candidateForce->force = $request->get('use_of_force');
        if ($candidateForce->force == 'Yes') {
            if (null !== $request->get('force_certification')) {
                $candidateForce->use_of_force_lookups_id = $request->get('force_certification');
            }

            $attach_documents = $request->force_file;
            if (!empty($attach_documents)) {
                if (is_numeric($attach_documents)) {
                    $candidateForce->attachment_id = $attach_documents;
                } else {
                    $file = $this->attachmentRepository->saveAttachmentFile('candidate-recruitment', $request, 'force_file');
                    $attachment_id = $file['file_id'];
                    $candidateForce->attachment_id = $attachment_id;
                }
            }

            if (null !== $request->get('force_expiry')) {
                $candidateForce->expiry = $request->get('force_expiry');
            }
        } else {
            $candidateForce->use_of_force_lookups_id = null;
            $candidateForce->attachment_id = null;
            $candidateForce->expiry = null;
        }

        $candidate = Candidate::find($candidate_id);
        $candidate->force()->delete();
        $candidate->force()->save($candidateForce);
    }

    /**
     * Store Candidate Wages
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeWageExpectation($request, $candidate_id)
    {
        $candidatewage = new CandidateWageExpectation;
        $candidatewage->candidate_id = $candidate_id;
        $candidatewage->wage_expectations_from = $request->get('wage_expectations_from');
        $candidatewage->wage_expectations_to = $request->get('wage_expectations_to');
        $candidatewage->wage_last_hourly = $request->get('wage_last_hourly');
        $candidatewage->wage_last_hours_per_week = $request->get('wage_last_hours_per_week');
        $candidatewage->current_paystub = $request->get('current_paystub');
        $candidatewage->wage_last_provider = $request->get('wage_last_provider');
        $candidatewage->wage_last_provider_other = $request->get('wage_last_provider_other');
        $candidatewage->last_role_held = $request->get('last_role_held');
        $candidatewage->explanation_wage_expectation = $request->get('explanation_wage_expectation');
        $candidatewage->security_provider_strengths = $request->get('security_provider_strengths');
        $candidatewage->security_provider_notes = $request->get('security_provider_notes');
        $candidatewage->rate_experience = $request->get('rate_experience');
        $candidate = Candidate::find($candidate_id);
        $candidate->wageexpectation()->delete();
        $candidate->wageexpectation()->save($candidatewage);
    }

    /**
     * Store Candidate Availability
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeAvailability($request, $candidate_id)
    {

        $candidateavailability = new CandidateAvailability;
        $candidateavailability->candidate_id = $candidate_id;
        $candidateavailability->current_availability = $request->get('current_availability');
        if ($request->get('days_required')) {
            $candidateavailability->days_required = json_encode($request->get('days_required'));
        } else {
            $candidateavailability->days_required = null;
        }

        if ($request->get('shifts')) {
            $candidateavailability->shifts = json_encode($request->get('shifts'));
        } else {
            $candidateavailability->shifts = null;
        }
        $candidateavailability->availability_explanation = $request->get('availability_explanation');
        $candidateavailability->availability_start = $request->get('availability_start');
        $candidateavailability->understand_shift_availability = $request->get('understand_shift_availability');
        $candidateavailability->available_shift_work = $request->get('available_shift_work');
        $candidateavailability->explanation_restrictions = $request->get('explanation_restrictions');
        $candidate = Candidate::find($candidate_id);
        $candidate->availability()->delete();
        $candidate->availability()->save($candidateavailability);
    }

    /**
     * Store Candidate Security Clearance
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeSecurityClearance($request, $candidate_id)
    {
        $candidatesecurityclearancee = new CandidateSecurityClearance;
        $candidatesecurityclearancee->candidate_id = $candidate_id;

        $candidatesecurityclearancee->born_outside_of_canada = $request->get('optradio') == 'Yes' ? 'Yes' : 'No';
        $candidatesecurityclearancee->work_status_in_canada = $request->get('work_status_in_canada');
        if ($request->get('work_status_in_canada') == "Landed Immigrant") {
            $candidatesecurityclearancee->status_expiry_date = $request->get('status_expiry_date');
            $candidatesecurityclearancee->renew_status = $request->get('renew_status');
        } else {
            $candidatesecurityclearancee->status_expiry_date = null;
            $candidatesecurityclearancee->renew_status = null;
        }
        $candidatesecurityclearancee->years_lived_in_canada = $request->get('years_lived_in_canada');
        $candidatesecurityclearancee->prepared_for_security_screening = $request->get('prepared_for_security_screening');
        $candidatesecurityclearancee->no_clearance = $request->get('no_clearance');
        $candidatesecurityclearancee->no_clearance_explanation = $request->get('no_clearance_explanation');
        $candidate = Candidate::find($candidate_id);
        $candidate->securityclearance()->delete();
        $candidate->securityclearance()->save($candidatesecurityclearancee);
    }

    /**
     * Store Candidate Security Proximities
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeSecurityProximity($request, $candidate_id)
    {

        $candidatesecurityproximity = new CandidateSecurityProximity;
        $candidatesecurityproximity->candidate_id = $candidate_id;
        $candidatesecurityproximity->driver_license = $request->get('driver_license');
        $candidatesecurityproximity->access_vehicle = $request->get('access_vehicle');
        $candidatesecurityproximity->access_public_transport = $request->get('access_public_transport');
        $candidatesecurityproximity->transportation_limitted = $request->get('transportation_limitted');
        $candidatesecurityproximity->explanation_transport_limit = $request->get('explanation_transport_limit');
        $candidate = Candidate::find($candidate_id);
        $candidate->securityproximity()->delete();
        $candidate->securityproximity()->save($candidatesecurityproximity);
    }

    /**
     * Store Candidate Employment History
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function saveEmployementHistory($request, $candidate_id)
    {

        $start_date = $request->get('employement_start_date');
        $end_date = $request->get('employement_end_date');
        $employer = $request->get('employer');
        $role = $request->get('employement_role');
        $duties = $request->get('employement_duties');
        $employement_reason = $request->get('employement_reason');
        foreach ($start_date as $key => $value) {
            $data[$key]['start_date'] = $start_date[$key];
            $data[$key]['end_date'] = $end_date[$key];
            $data[$key]['employer'] = $employer[$key];
            $data[$key]['role'] = $role[$key];
            $data[$key]['duties'] = $duties[$key];
            $data[$key]['reason'] = $employement_reason[$key];
            $data[$key]['candidate_id'] = $candidate_id;
        }
        CandidateEmploymentHistory::where(['candidate_id' => $candidate_id])->delete();
        CandidateEmploymentHistory::insert($data);
    }

    /**
     * Store Candidate Reference
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeReference($request, $candidate_id)
    {

        $reference_name = $request->get('reference_name');
        $reference_employer = $request->get('reference_employer');
        $reference_position = $request->get('reference_position');
        $contact_phone = $request->get('contact_phone');
        $contact_email = $request->get('contact_email');
        foreach ($reference_name as $key => $value) {
            $data[$key]['reference_name'] = $reference_name[$key];
            $data[$key]['reference_employer'] = $reference_employer[$key];
            $data[$key]['reference_position'] = $reference_position[$key];
            $data[$key]['contact_phone'] = $contact_phone[$key];
            $data[$key]['contact_email'] = $contact_email[$key];
            $data[$key]['candidate_id'] = $candidate_id;
        }
        CandidateReference::where(['candidate_id' => $candidate_id])->delete();
        CandidateReference::insert($data);
    }

    /**
     * Store Candidate Education
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeEducation($request, $candidate_id)
    {

        $start_date_education = $request->get('start_date_education');
        $end_date_education = $request->get('end_date_education');
        $grade = $request->get('grade');
        $program = $request->get('program');
        $school = $request->get('school');
        foreach ($start_date_education as $key => $value) {
            $data[$key]['start_date_education'] = $start_date_education[$key];
            $data[$key]['end_date_education'] = $end_date_education[$key];
            $data[$key]['grade'] = $grade[$key];
            $data[$key]['program'] = $program[$key];
            $data[$key]['school'] = $school[$key];
            $data[$key]['candidate_id'] = $candidate_id;
        }
        CandidateEducation::where(['candidate_id' => $candidate_id])->delete();
        CandidateEducation::insert($data);
    }

    /**
     * Store   Candidate Experiences
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeExperiences($request, $candidate_id)
    {

        $candidateexperience = new CandidateExperience;
        $candidateexperience->candidate_id = $candidate_id;
        $candidateexperience->current_employee_commissionaries = $request->get('current_employee_commissionaries');
        $candidateexperience->employee_number = $request->get('employee_number');
        $candidateexperience->currently_posted_site = $request->get('currently_posted_site');
        $candidateexperience->position = $request->get('position');
        $candidateexperience->hours_per_week = $request->get('hours_per_week');

        $candidateexperience->applied_employment = $request->get('applied_employment');
        $candidateexperience->position_applied = $request->get('position_applied');
        $candidateexperience->start_date_position_applied = $request->get('start_date_position_applied');
        $candidateexperience->end_date_position_applied = $request->get('end_date_position_applied');
        $candidateexperience->employed_by_corps = $request->get('employed_by_corps');
        $candidateexperience->position_employed = $request->get('position_employed');
        $candidateexperience->start_date_employed = $request->get('start_date_employed');
        $candidateexperience->end_date_employed = $request->get('end_date_employed');

        $candidateexperience->location_employed = $request->get('location_employed');
        $candidateexperience->employee_num = $request->get('employee_num');
        $candidateexperience->start_date_employed = $request->get('start_date_employed');
        $candidateexperience->end_date_employed = $request->get('end_date_employed');
        $candidate = Candidate::find($candidate_id);
        $candidate->experience()->delete();
        $candidate->experience()->save($candidateexperience);
    }

    /**
     * Store  Candidate Miscellaneous
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeMiscellaneous($request, $candidate_id)
    {

        $candidatemiscellaneous = new CandidateMiscellaneouses;
        $candidatemiscellaneous->candidate_id = $candidate_id;
        $candidatemiscellaneous->veteran_of_armedforce = $request->get('veteran_of_armedforce');
        $candidatemiscellaneous->service_number = $request->get('service_number');
        $candidatemiscellaneous->canadian_force = $request->get('canadian_force');
        $candidatemiscellaneous->enrollment_date = $request->get('enrollment_date');
        $candidatemiscellaneous->release_date = $request->get('release_date');

        $candidatemiscellaneous->item_release_number = $request->get('item_release_number');
        $candidatemiscellaneous->rank_on_release = $request->get('rank_on_release');
        $candidatemiscellaneous->military_occupation = $request->get('military_occupation');
        $candidatemiscellaneous->reason_for_release = $request->get('reason_for_release');
        $candidatemiscellaneous->spouse_of_armedforce = $request->get('spouse_of_armedforce');
        $candidatemiscellaneous->dismissed = $request->get('dismissed');
        $candidatemiscellaneous->explanation_dismissed = $request->get('explanation_dismissed');
        $candidatemiscellaneous->limitations = $request->get('limitations');
        $candidatemiscellaneous->limitation_explain = $request->get('limitation_explain');

        $candidatemiscellaneous->criminal_convicted = $request->get('criminal_convicted');
        $candidatemiscellaneous->offence = $request->get('offence');
        $candidatemiscellaneous->offence_date = $request->get('offence_date');
        $candidatemiscellaneous->offence_location = $request->get('offence_location');

        $candidatemiscellaneous->disposition_granted = $request->get('disposition_granted');
        $candidatemiscellaneous->career_interest = $request->get('career_interest');
        $candidatemiscellaneous->other_roles = $request->get('other_roles');
        $candidatemiscellaneous->is_indian_native = $request->get('is_indian_native');
        $candidate = Candidate::find($candidate_id);
        $candidate->miscellaneous()->delete();
        $candidate->miscellaneous()->save($candidatemiscellaneous);
    }

    /**
     * Store  Candidate Languages
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeLanguages($request, $candidate_id)
    {

        $candidate = Candidate::find($candidate_id);
        CandidateLanguage::where(['candidate_id' => $candidate_id])->delete();
        //Candidate Languages English
        $candidatelanguage = new CandidateLanguage;
        $candidatelanguage->candidate_id = $candidate_id;
        $candidatelanguage->language_id = 1;
        $candidatelanguage->speaking = $request->get('speaking_english');
        $candidatelanguage->reading = $request->get('reading_english');
        $candidatelanguage->writing = $request->get('writing_english');
        $candidate->languages()->save($candidatelanguage);

        //Candidate Languages French
        $candidatelanguage = new CandidateLanguage;
        $candidatelanguage->candidate_id = $candidate_id;
        $candidatelanguage->language_id = 2;
        $candidatelanguage->speaking = $request->get('speaking_french');
        $candidatelanguage->reading = $request->get('reading_french');
        $candidatelanguage->writing = $request->get('writing_french');
        //$candidate = Candidate::find($candidate_id);
        $candidate->languages()->save($candidatelanguage);
    }

    /**
     * Store  Candidate Skills
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeSkills($request, $candidate_id)
    {

        CandidateSkills::where(['candidate_id' => $candidate_id])->delete();
        $skills = $request->get('skill');
        foreach ($skills as $id => $skill) {
            $candidateskills = new CandidateSkills;
            $candidateskills->candidate_id = $candidate_id;
            $candidateskills->skill_id = $id;
            $candidateskills->skill_level = $skill;
            $candidateskills->save();
        }
    }

    public function storeOtherlanguage($request, $candidate_id)
    {
        $otherlanguages = $request->otherlanguages;
        $keeplanguagearray = [];
        if ($otherlanguages > 0) {
            for ($i = 0; $i < $otherlanguages; $i++) {
                $controlid = $i + 2;
                $languagecontrol = "language_" . $controlid;
                $languagespeakcontrol = "language_speak_" . $controlid;
                $languagereadcontrol = "language_read_" . $controlid;
                $languagewritecontrol = "language_write_" . $controlid;
                $languageid = $request->get($languagecontrol);
                $speakproficiency = $request->get($languagespeakcontrol);
                $readproficiency = $request->get($languagereadcontrol);
                $writeproficiency = $request->get($languagewritecontrol);
                //$keeplanguagearray[] = $languageid;
                array_push($keeplanguagearray, $languageid);

                CandidateScreeningOtherLanguages::updateOrCreate(
                    [
                        "candidate_id" => $candidate_id,
                        "language_id" => $languageid
                    ],
                    [
                        "candidate_id" => $candidate_id,
                        "language_id" => $languageid,
                        "speaking" => $speakproficiency,
                        "reading" => $readproficiency,
                        "writing" => $writeproficiency
                    ]
                );
            }
        }
        if ($otherlanguages > 0) {
            if (count($keeplanguagearray) > 0) {
                CandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)
                    ->whereNotIn("language_id", $keeplanguagearray)
                    ->delete();
            } else {
                CandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)->delete();
            }
        } else {
            CandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)->delete();
        }
    }
    /**
     * Store  Candidate uniform measures
     *
     * @param  \Illuminate\Http\UniformMeasureRequest $request
     * @return json
     */
    public function store_uniform_measures(UniformEntryRequest $request)
    {
        $session_obj = $request->session()->get('CANINFO');
        //dd($session_obj->id);
        $inputs = [];
        $uniformarray["candidate_id"] = $session_obj["candidate"]->id;
        $uniformarray["user_id"] = null;
        $uniformarray["gender"] = $request->get("gender");
        $uniformarray["shipping_address"] = $request->get("shipping_address");
        $uniformarray["uniform_scheduling_entry_id"] = null;
        $gender = $request->get("gender");
        // if ($gender == "male") {
        //     $dimensions = UniformSchedulingMeasurementPoints::whereNotIn("id", [6])->get();
        // } else {
        //     $dimensions = UniformSchedulingMeasurementPoints::get();
        // }
        $dimensions = UniformSchedulingMeasurementPoints::get();

        foreach ($dimensions as $dimension) {
            if ($request->get("uniformcontrol-" . $dimension->id)) {
                $inputs[$dimension->id] = (float)$request->get("uniformcontrol-" . $dimension->id) + (float)$request->get("point_decimal_value_" . $dimension->id);
            }
        }
        $uniformarray["input"] = $inputs;
        return $this->uniformMeasurementsRepository->store($uniformarray);
    }
    /**
     * Store  Candidate Screening Questions
     *
     * @param  \Illuminate\Http\ScreeningQuestionsRequest $request
     * @return json
     */
    public function store_screening_questions(ScreeningQuestionsRequest $request)
    {

        try {
            DB::beginTransaction();
            $session_obj = $request->session()->get('CANINFO');
            CandidateScreeningQuestion::where(['candidate_id' => $session_obj['candidate']->id])->delete();
            $answers = $request->get('answer');
            $scores = $request->get('_sc');
            foreach ($answers as $id => $question) {
                $candidatescreening = new CandidateScreeningQuestion;
                $candidatescreening->candidate_id = $session_obj['candidate']->id;
                $candidatescreening->question_id = $id;
                $candidatescreening->answer = $question;
                $candidatescreening->score = isset($scores[$id]) ? $scores[$id] : null;
                $candidatescreening->save();
            }
            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'message' => 'Your session has been expired.'));
        }
    }

    /**
     * Store  Candidate Attachments
     *
     * @param  \Illuminate\Http\AttachmentRequest $request
     * @return json
     */
    public function storeAttachment(AttachmentRequest $request)
    {
        try {
            DB::beginTransaction();
            $session_obj = $request->session()->get('CANINFO');
            $attachment_id = $request->get('attachment_id');
            $attachement_prefix = strtolower(str_replace(' ', '', $session_obj['candidate']->name)) . '_' . time() . '_';

            // CandidateAttachment::where(['candidate_id' => $session_obj['candidate']->id])->delete();
            if ($request->hasFile('attachment_file_name')) {
                $attachment = $request->file('attachment_file_name');

                // foreach ($attachments as $id => $attachment) {
                // $fileName = $attachement_prefix . '_' . $id . '_' . str_replace(' ', '', $attachment->getClientOriginalName());
                $fileName = $attachement_prefix . '_' . $attachment_id . '_' . $attachment[$attachment_id]->getClientOriginalName();
                $destinationPath = public_path() . '/attachments';
                $attachment[$attachment_id]->move($destinationPath, $fileName);
                // $candidateattachment = new CandidateAttachment;
                // $data['candidate_id'] = $session_obj['candidate']->id;
                // $data['attachment_id'] = $id;
                $data['attachment_file_name'] = $fileName;
                CandidateAttachment::updateOrCreate(array('attachment_id' => $attachment_id, 'candidate_id' => $session_obj['candidate']->id), $data);
                // }
            }
            DB::commit();
            $candidateJob = CandidateJob::where(['candidate_id' => $session_obj['candidate']->id, 'job_id' => $session_obj['job']->id])->first();
            return response()->json(array('success' => true, 'candidate_job_id' => $candidateJob->job_id, 'file_name' => $fileName, 'attachment_id' => $attachment_id));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**Store Latitude and Longitude of candidate
     *
     * @param Request $request
     * @return type
     */
    public function geoCode(Request $request)
    {
        try {
            DB::beginTransaction();
            $affetected_rows = Candidate::where(['id' => $request->get('candidate_id')])
                ->update(['lat' => $request->get('lat'), 'lng' => $request->get('lng')]);
            DB::commit();
            if ($affetected_rows) {
                return response()->json(array('success' => true, 'affected_rows' => $affetected_rows));
            } else {
                return response()->json(array('success' => false, 'affected_rows' => $affetected_rows));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**
     * Store  Candidate Personality
     *
     * @param  \Illuminate\Http\ScreeningQuestionsRequest $request
     * @return json
     */
    public function store_personality(Request $request)
    {

        try {
            $session_obj = $request->session()->get('CANINFO');
            $candidate_id = $session_obj['candidate']->id;
            $check_candidate_score = $this->personality_score_repository->checkScore($candidate_id);
            if ($check_candidate_score > 0) {
                return response()->json(array('success' => true, 'message' => 'Already calculated score'));
            }

            DB::beginTransaction();
            foreach ($request->arr as $question) {
                $personality_test['candidate_id'] = $candidate_id;
                $personality_test['question_id'] = $question['question_id'];
                $personality_test['question_option_id'] = $question['question_option_id'];
                $this->personality_inventory_repository->store($personality_test);
            }
            $score_result = $this->personality_score_repository->calculateScore($candidate_id);

            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'message' => 'Your session has been expired.'));
        }
    }

    /**
     * Store  Candidate Competency Matrix
     *
     * @param  \Illuminate\Http\ScreeningQuestionsRequest $request
     * @return json
     */
    public function store_competency_matrix(Request $request)
    {
        try {
            DB::beginTransaction();
            $session_obj = $request->session()->get('CANINFO');
            $candidate_id = $session_obj['candidate']->id;
            //$candidate_id = 123;
            foreach ($request->arr as $competency) {
                $competency_matrix['candidate_id'] = $candidate_id;
                $competency_matrix['competency_matrix_lookup_id'] = $competency['competency'];
                $competency_matrix['competency_matrix_rating_lookup_id'] = $competency['rating'];
                $this->candidate_screening_competency_matrix_repository->store($competency_matrix);
            }

            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'message' => 'Your session has been expired.'));
        }
    }

    public function testscore()
    {
        return $this->personality_score_repository->calculateScore(1);
    }

    /**
     * To send notificaion to candidates
     *
     * @param $candidate_id
     *  @param $filename
     * @return void
     */
    public function sendNotification($candidate_id, $filename)
    {

        $candidate = Candidate::where('id', $candidate_id)->first();
        $mail = Mail::to($candidate->email);
        $mail->send(new CandidateNotification($candidate, 'mail.candidate.notification', $filename));
    }

    /**
     * PDF generation
     * @param $candidateJob
     * @return $filename
     */
    public function pdfGenerate($candidateJob)
    {
//        $pdf = PDF::loadHTML('<h1>Blah</h1>')->setWarnings(true)->save('myfile1.pdf');;

        $pdf = PDF::loadView('hranalytics::job-application.application', compact('candidateJob'));
//      $pdf->save('myfile1.pdf');
        $file_path = $this->getAttachmentPath($candidateJob->candidate_id, $candidateJob->job_id);
        $candidateApplicationFilename = uniqid('candidate_application_') . ".pdf";
        $path = storage_path('app') . $this->directory_seperator . $file_path;
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
        $filename = $this->directory_seperator . $candidateApplicationFilename;
        $pdf->save($path . $filename);
        return $path . $filename;
    }

    /**
     * Function to get Attachment path
     * @param $candidate_id
     * @param $job_id
     * @return string
     */
    public function getAttachmentPath($candidate_id, $job_id)
    {
        return config('globals.candidate_application_folder') . $this->directory_seperator . $candidate_id . $this->directory_seperator . $job_id;
    }
}
