<?php

namespace Modules\Recruitment\Http\Controllers;

use App\Helpers\S3HelperService;
use App\Repositories\AttachmentRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Languages;
use Modules\Recruitment\Models\RecUniformMeasurementPoint;
use Modules\Admin\Repositories\UserRepository;
use Modules\Recruitment\Http\Requests\RecCandidateRequest;
use Modules\Recruitment\Http\Requests\RecScreeningQuestionsRequest;
use Modules\Recruitment\Models\RecCandidate;
use Modules\Recruitment\Models\RecCandidateAddress;
use Modules\Recruitment\Models\RecCandidateAttachment;
use Modules\Recruitment\Models\RecCandidateAvailability;
use Modules\Recruitment\Models\RecCandidateAwareness;
use Modules\Recruitment\Models\RecCandidateCommissionairesUnderstanding;
use Modules\Recruitment\Models\RecCandidateEducation;
use Modules\Recruitment\Models\RecCandidateEmploymentHistory;
use Modules\Recruitment\Models\RecCandidateExperience;
use Modules\Recruitment\Models\RecCandidateForceCertification;
use Modules\Recruitment\Models\RecCandidateJobDetails;
use Modules\Recruitment\Models\RecCandidateLanguage;
use Modules\Recruitment\Models\RecCandidateMiscellaneouses;
use Modules\Recruitment\Models\RecCandidateReferalAvailability;
use Modules\Recruitment\Models\RecCandidateReference;
use Modules\Recruitment\Models\RecCandidateScreeningOtherLanguages;
use Modules\Recruitment\Models\RecCandidateScreeningQuestion;
use Modules\Recruitment\Models\RecCandidateSecurityClearances;
use Modules\Recruitment\Models\RecCandidateSecurityGuardingExperince;
use Modules\Recruitment\Models\RecCandidateSecurityProximity;
use Modules\Recruitment\Models\RecCandidateSkill;
use Modules\Recruitment\Models\RecCandidateWageExpectation;
use Modules\Recruitment\Repositories\RecCandidateScreeningCompetencyMatrixRepository;
use Modules\Recruitment\Repositories\RecCandidateScreeningPersonalityInventoryRepository;
use Modules\Recruitment\Repositories\RecCandidateScreeningPersonalityScoreRepository;
use Modules\Recruitment\Repositories\RecCustomerUniformKitRepository;

class RecJobApplicationController extends Controller
{

    /**
     * Create a new  instance.
     *
     *
     */
    public function __construct(
        RecCandidateScreeningCompetencyMatrixRepository $candidateScreeningCompetencyMatrixRepository,
        UserRepository $userRepository,
        RecCustomerUniformKitRepository $recCustomerUniformKitRepository,
        AttachmentRepository $attachmentRepository,
        S3HelperService $s3HelperService
    ) {
        $this->personality_score_repository = new RecCandidateScreeningPersonalityScoreRepository();
        $this->personality_inventory_repository = new RecCandidateScreeningPersonalityInventoryRepository();
        // $this->competency_matrix_lookup_repository = $competencyMatrixLookupRepository;
        // $this->competency_matrix_rating_lookup_repository = $competencyMatrixRatingLookupRepository;
        $this->candidate_screening_competency_matrix_repository = $candidateScreeningCompetencyMatrixRepository;

        // $this->commissionaires_understanding_lookup_repository = $commissionairesUnderstandingLookupRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->userRepository = $userRepository;
        // $this->directory_seperator = "/";
        // $this->extension_seperator = ".";
        $this->recCustomerUniformKitRepository = $recCustomerUniformKitRepository;
        $this->s3HelperService = $s3HelperService;
    }

    public function storeOtherlanguage($request, $candidate_id)
    {
        if (isset($request['languages'])) {
            $otherlanguages = json_decode($request['languages']);
            foreach ($otherlanguages as $key => $value) {
                $data[$key]['language_id'] = $value->language;
                $data[$key]['speaking'] = $value->language_speak;
                $data[$key]['reading'] = $value->language_read;
                $data[$key]['writing'] = $value->language_write;
                $data[$key]['candidate_id'] = $candidate_id;
            }
            RecCandidateScreeningOtherLanguages::where(['candidate_id' => $candidate_id])->delete();
            if (sizeof($otherlanguages) > 0) {
                RecCandidateScreeningOtherLanguages::insert($data);
            }
        } else {
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

                    RecCandidateScreeningOtherLanguages::updateOrCreate(
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
                    RecCandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)
                        ->whereNotIn("language_id", $keeplanguagearray)
                        ->delete();
                } else {
                    RecCandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)->delete();
                }
            } else {
                RecCandidateScreeningOtherLanguages::where("candidate_id", $candidate_id)->delete();
            }
        }
    }

    /**
     * PDF view of Application
     *
     * @param Illuminate\Http\Request;
     * @param  $id
     * @return view
     */
    public function viewApplication($id, Request $request)
    {
        $session_obj = $request->session()->get('CANINFO');
        if (isset($session_obj) && isset($session_obj['candidate']) && null != $session_obj['candidate']) {
            $candidateJob = RecCandidateAwareness::with(
                'candidate',
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
                'candidate.interviewnote',
                'candidate.comissionaires_understanding'
            )
                ->where(['candidate_id' => $session_obj['candidate']->id, 'job_id' => $id])
                ->first();
            $candidateJob->status = 'Applied';
            $candidateJob->submitted_date = Carbon::now();
            $candidateJob->save();
            $session_obj = null;
            $request->session()->put('CANINFO', $session_obj);

            $candidate_application_filename = $this->pdfGenerate($candidateJob);
            $this->sendNotification($candidateJob->candidate_id, $candidate_application_filename);

            return view('recruitment::job-application.partials.view_pdf', compact('candidateJob'));
        } else {
            $session_obj = null;
            $request->session()->put('CANINFO', $session_obj);
            return redirect(route('applyjob'));
        }
    }

    /**
     * Function to check Candidate is already applied or not
     * @param $email
     * @param $phone_home
     * @param $phone_cellular
     * @param $job_id
     * @return \Illuminate\Http\Response
     */
    public function isCandidateAlreadyApplied($email, $phone_home, $phone_cellular, $job_id)
    {

        $candidate = RecCandidate::where(['email' => $email])->first();
        /*if ($candidate == null && !empty($phone_home)) {
        $candidate = Candidate::where(['phone_home' => $phone_home])->first();
        }
        if ($candidate == null && !empty($phone_cellular)) {
        $candidate = Candidate::where(['phone_cellular' => $phone_cellular])->first();
        }*/
        $candidate_id = ($candidate != null) ? $candidate->id : 0;

        return (RecCandidateAwareness::where('candidate_id', '=', $candidate_id)

            // ->orwhere('job_id', '=', $job_id)
            ->where('status', "=", "Applied")
            ->count());
    }

    /**
     * Store  Candidate Attachments
     *
     * @param \Illuminate\Http\RecAttachmentRequest $request
     * @return json
     */
    public function storeAttachment(Request $request)
    {
        try {
            DB::beginTransaction();
            // $session_obj = $request->session()->get('CANINFO');
            // $attachment_id = $request->get('attachment_id');
            // $attachement_prefix = strtolower(str_replace(' ', '', $session_obj['candidate']->name)) . '_' . time() . '_';

            // // CandidateAttachment::where(['candidate_id' => $session_obj['candidate']->id])->delete();
            // if ($request->hasFile('attachment_file_name')) {
            //     $attachment = $request->file('attachment_file_name');

            //     // foreach ($attachments as $id => $attachment) {
            //     // $fileName = $attachement_prefix . '_' . $id . '_' . str_replace(' ', '', $attachment->getClientOriginalName());
            //     $fileName = $attachement_prefix . '_' . $attachment_id . '_' . $attachment[$attachment_id]->getClientOriginalName();
            //     $destinationPath = public_path() . '/attachments';
            //     $attachment[$attachment_id]->move($destinationPath, $fileName);
            //     // $candidateattachment = new CandidateAttachment;
            //     // $data['candidate_id'] = $session_obj['candidate']->id;
            //     // $data['attachment_id'] = $id;
            //     $data['attachment_file_name'] = $fileName;
            //     RecCandidateAttachment::updateOrCreate(array('attachment_id' => $attachment_id, 'candidate_id' => $session_obj['candidate']->id), $data);
            //     // }
            // }
            $session_obj = $request->session()->get('CANINFO');
            if ($request['items']) {
                foreach ($request['items'] as $key => $attchment) {
                    $data['attachment_file_name'] = $attchment['filename'];
                    RecCandidateAttachment::updateOrCreate(array('attachment_id' => $attchment['id'], 'candidate_id' => $session_obj['candidate']->id), $data);
                }
            }
            DB::commit();
            $candidateJob = RecCandidateAwareness::where(['candidate_id' => $session_obj['candidate']->id])
                //'job_id' => $session_obj['job']->id])
                ->first();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(RecCandidateRequest $request)
    {
        $session_obj = $request->session()->get('CANINFO');
        // try {
        DB::beginTransaction();
        // $already_applied = $this->isCandidateAlreadyApplied($request->get('email'), $request->get('phone_home'), $request->get('phone_cellular'), $session_obj['job']->id);
        $already_applied = 0;

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
            'phone' => $request->get('phone'),
            'phone_cellular' => $request->get('phone_cellular'),
            'smart_phone_type_id' => $request->get('smart_phone_type_id'),
            'smart_phone_skill_level' => $request->get('smart_phone_skill_level'),
            'profile_image' => null,
        );
        $candidate = RecCandidate::where(['email' => $request->get('email')])->first();
        if ($candidate === null) {
            $candidate = RecCandidate::create($insertCandidate);
        } else {
            $insertCandidate['profile_image'] = $candidate->profile_image;
            $candidate = RecCandidate::updateOrCreate(['id' => (int)$candidate->id], $insertCandidate);
        }

        $session_obj['candidate'] = $candidate;
        $request->session()->put('CANINFO', $session_obj);
        $candidate_id = $session_obj['candidate']->id;


        //candidate image upload
        $candidateImageName = $this->userRepository->uploadProfileImage($request, $candidate_id, 'candidate_profile');
        if ($candidateImageName != null) {
            $insertCandidate['profile_image'] = $candidateImageName;
        }
        RecCandidate::updateOrCreate(['id' => (int)$candidate_id], $insertCandidate);
        $this->storeJob($request, $candidate_id);
        if (null !== $request->get('prev_address')) {
            $this->storeAddress($request, $candidate_id);
        }
        $this->storeCandidateReferalAvailability($request, $candidate_id);
        $this->storeCandidateCommissionairesUnderstanding($request->all(), $candidate_id);
        $this->storeCandidateForceCertification($request, $candidate_id);
        $this->storeSecurityExperience($request->all(), $candidate_id);
        $this->storeWageExpectation($request->all(), $candidate_id);
        $this->storeAvailability($request->all(), $candidate_id);

        $this->storeSecurityClearance($request->all(), $candidate_id);
        $this->storeSecurityProximity($request->all(), $candidate_id);
        $this->saveEmployementHistory($request->all(), $candidate_id);
        $this->storeReference($request->all(), $candidate_id);
        $this->storeEducation($request->all(), $candidate_id);
        $this->storeExperiences($request->all(), $candidate_id);
        $this->storeMiscellaneous($request->all(), $candidate_id);
        $this->storeLanguages($request->all(), $candidate_id);
        $this->storeSkills($request->all(), $candidate_id);
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

    /**
     * Store Candidate Commissionaires Understanding
     * @param  $Understandings , $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeCandidateCommissionairesUnderstanding($request, $candidate_id)
    {

        $data['commissionaires_understanding_lookups_id'] = $request['candidate_commissionaires_understandings_id'];
        $data['candidate_id'] = $candidate_id;
        RecCandidateCommissionairesUnderstanding::where(['candidate_id' => $candidate_id])->delete();
        RecCandidateCommissionairesUnderstanding::create($data);
    }

    public function storeCandidateReferalAvailability($request, $candidate_id)
    {
        $candidateavailability = new RecCandidateReferalAvailability;
        $candidateavailability->candidate_id = $candidate_id;
        $candidateavailability->orientation = $request['orientation'];
        $candidateavailability->job_post_finding = $request['job_post_finding'];
        $candidateavailability->sponser_email = $request['sponser_email'];
        $candidateavailability->position_availibility = $request['position_availibility'];
        $candidateavailability->floater_hours = $request['floater_hours'];
        $candidateavailability->starting_time = $request['starting_time'];
        $candidate = RecCandidate::find($candidate_id);
        $candidate->referalAvailibility()->delete();
        $candidate->referalAvailibility()->save($candidateavailability);
    }

    public function storeCandidate($request, $candidate_id)
    {
        $candidate['address'] = $request['address'];
        $candidate['city'] = $request['city'];
        $candidate['postal_code'] = $request['postal_code'];
        $candidate['geo_location_lat'] = $request['geo_location_lat'];
        $candidate['geo_location_long'] = $request['geo_location_long'];
        $candidate['smart_phone_type_id'] = $request['smart_phone_type_id'];
        $candidate['smart_phone_skill_level'] = $request['smart_phone_skill_level'];
        RecCandidate::where(['id' => $candidate_id])->update($candidate);
    }

    /**
     * Store candidate force certification
     */
    public function storeCandidateForceCertification($request, $candidate_id)
    {
        $candidateForce = new RecCandidateForceCertification;
        $candidateForce->candidate_id = $candidate_id;
        $candidateForce->force = $request['use_of_force'];
        if ($candidateForce->force == 'Yes') {
            if (null !== $request['use_of_force_lookups_id']) {
                $candidateForce->use_of_force_lookups_id = $request['use_of_force_lookups_id'];
            }
            if (!empty($request['uof_path'])) {
                if (strpos($request['uof_path'], "temp") > 0) {
                    // delete existing file
                    $candidate = RecCandidate::find($candidate_id);
                    if (isset($candidate->force->s3_location_path)) {
                        $existingFilePath = $candidate->force->s3_location_path;
                        S3HelperService::trashFile('s3-recruitment', $existingFilePath);
                    }
                    // save new file
                    $pathArr = explode('/', $request['uof_path']);
                    $fileWithPrefix = implode("/", array_slice($pathArr, 4));
                    $srcPath = "temp/" . $fileWithPrefix;
                    \Storage::disk('s3-recruitment')->move($srcPath, $fileWithPrefix);
                } else {
                    $fileWithPrefix = $request['uof_path'];
                }
                $candidateForce->s3_location_path = $fileWithPrefix;
            }

            if (null !== $request['force_expiry']) {
                $candidateForce->expiry = $request['force_expiry'];
            }
        } else {
            $candidateForce->use_of_force_lookups_id = null;
            $candidateForce->attachment_id = null;
            $candidateForce->expiry = null;
        }

        $candidate = RecCandidate::find($candidate_id);
        $candidate->force()->delete();
        $candidate->force()->save($candidateForce);
    }

    /**
     * Store  Candidate uniform measures
     *
     * @param \Illuminate\Http\UniformMeasureRequest $request
     * @return json
     */
    public function store_uniform_measures(Request $request)
    {
        try {
            DB::beginTransaction();
            $session_obj = $request->session()->get('CANINFO');
            // $inputs = [];
            $candidate_id = (null!==$request->get("candidate_id"))?$request->get("candidate_id"):$session_obj["candidate"]->id;
            $uniformarray["user_id"] = null;
            $uniformarray["gender"] = $request->get("gender");
            $uniformarray["address"] = $request->get("shipping_address");
            $gender = $request->get("gender");
            // if ($gender == "male") {
            //     $dimensions = UniformSchedulingMeasurementPoints::whereNotIn("id", [6])->get();
            // } else {
            //     $dimensions = UniformSchedulingMeasurementPoints::get();
            // }
            $dimensions = RecUniformMeasurementPoint::get();

            foreach ($dimensions as $dimension) {
                if ($request->get("uniformcontrol-" . $dimension->id)) {
                    $inputs[$dimension->id] = (float)$request->get("uniformcontrol-" . $dimension->id) + (float)$request->get("point_decimal_value_" . $dimension->id);
                }
            }
            $uniformarray["item_measurement"][0] = $inputs;
            $job_details = RecCandidateJobDetails::select('job_id')->where('status', '=', 3)->where('candidate_id', '=', $candidate_id)->first();
            $uniformarray["job_id"]= $job_details['job_id'];
            $saveUniform= $this->recCustomerUniformKitRepository->saveUniformDetails($uniformarray, $candidate_id, $uniformarray["job_id"]);


            DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return response()->json(array('success' => false, 'message' => 'Your session has been expired.'));
        }
    }

    /**
     * Store Candidate Guarding Experiences
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeSecurityExperience($request, $candidate_id)
    {
        $candidateguardingexperience = new RecCandidateSecurityGuardingExperince;
        $candidateguardingexperience->candidate_id = $candidate_id;
        $candidateguardingexperience->guard_licence = $request['guard_licence'];

        $candidateguardingexperience->start_date_guard_license = 'null' !== ($request['start_date_guard_license']) ? $request['start_date_guard_license'] : null;
        $candidateguardingexperience->start_date_first_aid =
            'null' !== ($request['start_date_first_aid']) ? $request['start_date_first_aid'] : null;
        $candidateguardingexperience->start_date_cpr = 'null' !== ($request['start_date_cpr']) ? $request['start_date_cpr'] : null;
        $candidateguardingexperience->expiry_guard_license = 'null' !== ($request['expiry_guard_license']) ? $request['expiry_guard_license'] : null;
        $candidateguardingexperience->expiry_first_aid = 'null' !== ($request['expiry_first_aid']) ? $request['expiry_first_aid'] : null;
        $candidateguardingexperience->expiry_cpr = 'null' !== ($request['expiry_cpr']) ? $request['expiry_cpr'] : null;
        $candidateguardingexperience->test_score_percentage = null!==($request['test_score_percentage'])?$request['test_score_percentage']:null;
        $request['candidate_id'] = $candidate_id;
        if (!empty($request['test_score_path'])) {
            if (strpos($request['test_score_path'], "temp") > 0) {
                // delete existing file
                $candidate = RecCandidate::find($candidate_id);
                if (isset($candidate->guardingExperience->test_score_path)) {
                    $existingFilePath = $candidate->guardingExperience->test_score_path;
                    S3HelperService::trashFile('s3-recruitment', $existingFilePath);
                }
                // save new file
                $pathArr = explode('/', $request['test_score_path']);
                $fileWithPrefix = implode("/", array_slice($pathArr, 4));
                $srcPath = "temp/" . $fileWithPrefix;
                \Storage::disk('s3-recruitment')->move($srcPath, $fileWithPrefix);
            } else {
                $fileWithPrefix = $request['test_score_path'];
            }
            $candidateguardingexperience->test_score_path = $fileWithPrefix;
        }
        if (isset($request['security_clearance'])) {
            $candidateguardingexperience->security_clearance = $request['security_clearance'];
        } else {
            $candidateguardingexperience->security_clearance = null;
        }
        if ($candidateguardingexperience->security_clearance == 'Yes') {
            $candidateguardingexperience->security_clearance_type = $request['security_clearance_type'];
            $candidateguardingexperience->security_clearance_expiry_date = $request['security_clearance_expiry_date'];
        } else {
            $candidateguardingexperience->sin_expiry_date_status = null;
            $candidateguardingexperience->sin_expiry_date = null;
        }
        $candidateguardingexperience->social_insurance_number = $request['social_insurance_number'];

        if ($candidateguardingexperience->social_insurance_number == 1) {
            $candidateguardingexperience->sin_expiry_date_status = $request['sin_expiry_date_status'] == '1' ? 1 : 0;
            if ($candidateguardingexperience->sin_expiry_date_status == 1) {
                $candidateguardingexperience->sin_expiry_date = $request['sin_expiry_date'];
            } else {
                $candidateguardingexperience->sin_expiry_date = null;
            }
        } else {
            $candidateguardingexperience->sin_expiry_date_status = null;
            $candidateguardingexperience->sin_expiry_date = null;
        }

        $candidateguardingexperience->years_security_experience = $request['years_security_experience'];
        $candidateguardingexperience->most_senior_position_held = $request['most_senior_position_held'];
        //$candidateguardingexperience->most_senior_position_held = 1;

        /* positions held in past */
        $positions_lookups = DB::table('position_lookups')->whereNull('deleted_at')->orderBy('position', 'asc')->pluck('position', 'id')->toArray() + array(0 => 'Other');
        if (isset($request['positions_experinces'])) {
            $positions_experinces = json_decode($request['positions_experinces']);
            foreach ($positions_experinces as $id => $each_position) {
                if (isset($positions_lookups[$id])) {
                    $control_name = str_replace(' ', '_', strtolower($positions_lookups[$id]));
                    $array_position[$control_name] = $each_position;
                }
            }
        } else {
            $positions_experinces = $positions_lookups;
            foreach ($positions_lookups + array(0 => 'Other') as $each_position) {
                $control_name = str_replace(' ', '_', strtolower($each_position));
                $array_position[$control_name] = $request[$control_name];
            }
        }


        //$array_position = array('site_supervisor' => $request->get('site_supervisor'), 'shift_leader' => $request->get('shift_leader'), 'foot_patrol' => $request->get('foot_patrol'), 'concierge' => $request->get('concierge'), 'security_guard' => $request->get('security_guard'), 'access_control' => $request->get('access_control'), 'cctv_operator' => $request->get('cctv_operator'), 'mobile_patrols' => $request->get('mobile_patrols'), 'investigations' => $request->get('investigations'), 'loss_prevention_officer' => $request->get('loss_prevention_officer'), 'operations' => $request->get('operations'), 'dispatch' => $request->get('dispatch'), 'other' => $request->get('other'));
        // $array_position[$control_name] = $request['other'];
        $candidateguardingexperience->positions_experinces = json_encode($array_position);

        $candidate = RecCandidate::find($candidate_id);
        $candidate->guardingexperience()->delete();
        $candidate->guardingexperience()->save($candidateguardingexperience);
    }


    /**
     * Store Candidate Wages
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeWageExpectation($request, $candidate_id)
    {
        $candidatewage = new RecCandidateWageExpectation;
        $candidatewage->candidate_id = $candidate_id;
        $candidatewage->wage_expectations = $request['wage_expectations'];
        $candidatewage->wage_last_hourly = $request['wage_last_hourly'];
        $candidatewage->wage_last_hours_per_week = isset($request['wage_last_hours_per_week'])?$request['wage_last_hours_per_week']:null;
        $candidatewage->current_paystub = $request['current_paystub'];
        $candidatewage->wage_last_provider = $request['wage_last_provider'];
        $candidatewage->wage_last_provider_other = $request['wage_last_provider_other'];
        $candidatewage->wage_last_hours_per_week = $request['wage_last_hours_per_week'];
        $candidatewage->last_role_held = $request['last_role_held'];
        $candidatewage->explanation_wage_expectation = $request['explanation_wage_expectation'];
        $candidatewage->security_provider_strengths = $request['security_provider_strengths'];
        $candidatewage->security_provider_notes = $request['security_provider_notes'];
        $candidatewage->rate_experience = $request['rate_experience'];
        $candidate = RecCandidate::find($candidate_id);
        $candidate->wageexpectation()->delete();
        $candidate->wageexpectation()->save($candidatewage);
    }

    /**
     * Store Candidate Availability
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeAvailability($request, $candidate_id)
    {

        $candidateavailability = new RecCandidateAvailability;
        $candidateavailability->candidate_id = $candidate_id;
        $candidateavailability->current_availability = $request['current_availability'];
        if (!is_array($request['days_required'])) {
            $days_required = json_decode($request['days_required']);
            $candidateavailability->days_required = implode(',', array_column($days_required, 'label'));
        } else {
            $candidateavailability->days_required = trim(implode(',', $request['days_required']));
        }
        if (!is_array($request['shifts'])) {
            $shifts = json_decode($request['shifts']);
            $candidateavailability->shifts = implode(',', array_column($shifts, 'label'));
        } else {
            $candidateavailability->shifts = trim(implode(',', $request['shifts']));
        }

        $candidateavailability->availability_explanation = $request['availability_explanation'];
        $candidateavailability->availability_start = $request['availability_start'];
        $candidateavailability->understand_shift_availability = $request['understand_shift_availability'];
        $candidateavailability->available_shift_work = $request['available_shift_work'];
        $candidateavailability->explanation_restrictions = $request['explanation_restrictions'];
        $candidate = RecCandidate::find($candidate_id);
        $candidate->availability()->delete();
        $candidate->availability()->save($candidateavailability);
    }

    /**
     * Store Candidate Security Clearance
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeSecurityClearance($request, $candidate_id)
    {
        $candidatesecurityclearancee = new RecCandidateSecurityClearances;
        $candidatesecurityclearancee->candidate_id = $candidate_id;

        $candidatesecurityclearancee->born_outside_of_canada = $request['optradio'] == '1' ? 'Yes' : 'No';
        $candidatesecurityclearancee->work_status_in_canada = $request['work_status_in_canada'];
        if ($request['work_status_in_canada'] == "Landed Immigrant") {
            $candidatesecurityclearancee->status_expiry_date = $request['status_expiry_date'];
            $candidatesecurityclearancee->renew_status = $request['renew_status'];
        } else {
            $candidatesecurityclearancee->status_expiry_date = null;
            $candidatesecurityclearancee->renew_status = null;
        }
        $candidatesecurityclearancee->years_lived_in_canada = $request['years_lived_in_canada'];
        $candidatesecurityclearancee->prepared_for_security_screening = $request['prepared_for_security_screening'];
        $candidatesecurityclearancee->no_clearance = $request['no_clearance'];
        $candidatesecurityclearancee->no_clearance_explanation = $request['no_clearance_explanation'];
        $candidate = RecCandidate::find($candidate_id);
        $candidate->securityclearance()->delete();
        $candidate->securityclearance()->save($candidatesecurityclearancee);
    }

    /**
     * Store Candidate Security Proximities
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeSecurityProximity($request, $candidate_id)
    {

        $candidatesecurityproximity = new RecCandidateSecurityProximity;
        $candidatesecurityproximity->candidate_id = $candidate_id;
        $candidatesecurityproximity->driver_license = $request['driver_license'];
        $candidatesecurityproximity->access_vehicle = $request['access_vehicle'];
        $candidatesecurityproximity->access_public_transport = $request['access_public_transport'];
        $candidatesecurityproximity->transportation_limitted = $request['transportation_limitted'];
        $candidatesecurityproximity->explanation_transport_limit = $request['explanation_transport_limit'];
        $candidate = RecCandidate::find($candidate_id);
        $candidate->securityproximity()->delete();
        $candidate->securityproximity()->save($candidatesecurityproximity);
    }

    /**
     * Store Candidate Employment History
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function saveEmployementHistory($request, $candidate_id)
    {
        if (isset($request['workExperiences'])) {
            $workExperiences = json_decode($request['workExperiences']);
            foreach ($workExperiences as $key => $value) {
                $data[$key]['start_date'] = $value->employementStartDate;
                $data[$key]['end_date'] = $value->employementEndDate;
                $data[$key]['employer'] = $value->employer;
                $data[$key]['role'] = $value->employementRole;
                $data[$key]['duties'] = $value->employementDuties;
                $data[$key]['reason'] = $value->employementReason;
                $data[$key]['candidate_id'] = $candidate_id;
            }
        } else {
            $start_date = $request['employement_start_date'];
            $end_date = $request['employement_end_date'];
            $employer = $request['employer'];
            $role = $request['employement_role'];
            $duties = $request['employement_duties'];
            $employement_reason = $request['employement_reason'];
            foreach ($start_date as $key => $value) {
                $data[$key]['start_date'] = $start_date[$key];
                $data[$key]['end_date'] = $end_date[$key];
                $data[$key]['employer'] = $employer[$key];
                $data[$key]['role'] = $role[$key];
                $data[$key]['duties'] = $duties[$key];
                $data[$key]['reason'] = $employement_reason[$key];
                $data[$key]['candidate_id'] = $candidate_id;
            }
        }
        RecCandidateEmploymentHistory::where(['candidate_id' => $candidate_id])->delete();
        RecCandidateEmploymentHistory::insert($data);
    }

    /**
     * Store Candidate Reference
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeReference($request, $candidate_id)
    {
        if (isset($request['references'])) {
            $references = json_decode($request['references']);
            foreach ($references as $key => $value) {
                $data[$key]['reference_name'] = $value->name;
                $data[$key]['reference_employer'] = $value->employeer;
                $data[$key]['reference_position'] = $value->position;
                $data[$key]['contact_phone'] = $value->contact;
                $data[$key]['contact_email'] = $value->email;
                $data[$key]['candidate_id'] = $candidate_id;
            }
        } else {
            $reference_name = $request['reference_name'];
            $reference_employer = $request['reference_employer'];
            $reference_position = $request['reference_position'];
            $contact_phone = $request['contact_phone'];
            $contact_email = $request['contact_email'];
            foreach ($reference_name as $key => $value) {
                $data[$key]['reference_name'] = $reference_name[$key];
                $data[$key]['reference_employer'] = $reference_employer[$key];
                $data[$key]['reference_position'] = $reference_position[$key];
                $data[$key]['contact_phone'] = $contact_phone[$key];
                $data[$key]['contact_email'] = $contact_email[$key];
                $data[$key]['candidate_id'] = $candidate_id;
            }
        }
        RecCandidateReference::where(['candidate_id' => $candidate_id])->delete();
        RecCandidateReference::insert($data);
    }

    /**
     * Store Candidate Education
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeEducation($request, $candidate_id)
    {
        if (isset($request['educations'])) {
            $educations = json_decode($request['educations']);
            foreach ($educations as $key => $value) {
                $data[$key]['start_date_education'] = $value->startDateEducation;
                $data[$key]['end_date_education'] = $value->endDateEducation;
                $data[$key]['grade'] = $value->grade;
                $data[$key]['program'] = $value->program;
                $data[$key]['school'] = $value->school;
                $data[$key]['candidate_id'] = $candidate_id;
            }
        } else {
            $start_date_education = $request['start_date_education'];
            $end_date_education = $request['end_date_education'];
            $grade = $request['grade'];
            $program = $request['program'];
            $school = $request['school'];
            foreach ($start_date_education as $key => $value) {
                $data[$key]['start_date_education'] = $start_date_education[$key];
                $data[$key]['end_date_education'] = $end_date_education[$key];
                $data[$key]['grade'] = $grade[$key];
                $data[$key]['program'] = $program[$key];
                $data[$key]['school'] = $school[$key];
                $data[$key]['candidate_id'] = $candidate_id;
            }
        }
        RecCandidateEducation::where(['candidate_id' => $candidate_id])->delete();
        RecCandidateEducation::insert($data);
    }

    /**
     * Store   Candidate Experiences
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeExperiences($request, $candidate_id)
    {

        $candidateexperience = new RecCandidateExperience;
        $candidateexperience->candidate_id = $candidate_id;
        $candidateexperience->current_employee_commissionaries = $request['current_employee_commissionaries'];
        $candidateexperience->employee_number = $request['employee_number'];
        $candidateexperience->currently_posted_site = $request['currently_posted_site'];
        $candidateexperience->position = $request['position'];
        $candidateexperience->hours_per_week = $request['hoursper_week'];

        $candidateexperience->applied_employment = $request['applied_employment'];
        $candidateexperience->position_applied = $request['position_applied'];
        $candidateexperience->start_date_position_applied = $request['start_date_position_applied'];
        $candidateexperience->end_date_position_applied = $request['end_date_position_applied'];
        $candidateexperience->employed_by_corps = $request['employed_by_corps'];
        $candidateexperience->position_employed = $request['position_employed'];
        $candidateexperience->start_date_employed = $request['start_date_employed'];
        $candidateexperience->end_date_employed = $request['end_date_employed'];

        $candidateexperience->location_employed = $request['location_employed'];
        $candidateexperience->employee_num = $request['employee_num'];
        $candidateexperience->start_date_employed = $request['start_date_employed'];
        $candidateexperience->end_date_employed = $request['end_date_employed'];
        $candidate = RecCandidate::find($candidate_id);
        $candidate->experience()->delete();
        $candidate->experience()->save($candidateexperience);
    }

    /**
     * Store  Candidate Miscellaneous
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeMiscellaneous($request, $candidate_id)
    {

        $candidatemiscellaneous = new RecCandidateMiscellaneouses;
        $candidatemiscellaneous->candidate_id = $candidate_id;
        $candidatemiscellaneous->veteran_of_armedforce = $request['veteran_of_armedforce'];
        $candidatemiscellaneous->service_number = $request['service_number'];
        $candidatemiscellaneous->canadian_force = $request['canadian_force'];
        $candidatemiscellaneous->enrollment_date = $request['enrollment_date'];
        $candidatemiscellaneous->release_date = $request['release_date'];

        $candidatemiscellaneous->item_release_number = $request['item_release_number'];
        $candidatemiscellaneous->rank_on_release = $request['rank_on_release'];
        $candidatemiscellaneous->military_occupation = $request['military_occupation'];
        $candidatemiscellaneous->reason_for_release = $request['reason_for_release'];
        $candidatemiscellaneous->spouse_of_armedforce = $request['spouse_of_armedforce'];
        $candidatemiscellaneous->dismissed = $request['dismissed'];
        $candidatemiscellaneous->explanation_dismissed = $request['explanation_dismissed'];
        $candidatemiscellaneous->limitations = $request['limitations'];
        $candidatemiscellaneous->limitation_explain = $request['limitation_explain'];

        $candidatemiscellaneous->criminal_convicted = $request['criminal_convicted'];
        $candidatemiscellaneous->offence = $request['offence'];
        $candidatemiscellaneous->offence_date = $request['offence_date'];
        $candidatemiscellaneous->offence_location = $request['offence_location'];

        $candidatemiscellaneous->career_interest = $request['career_interest'];
        $candidatemiscellaneous->other_roles = $request['other_roles'];
        $candidatemiscellaneous->is_indian_native = $request['is_indian_native'];
        $candidate = RecCandidate::find($candidate_id);
        $candidate->miscellaneous()->delete();
        $candidate->miscellaneous()->save($candidatemiscellaneous);
    }

    /**
     * Store  Candidate Languages
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeLanguages($request, $candidate_id)
    {

        $candidate = RecCandidate::find($candidate_id);
        RecCandidateLanguage::where(['candidate_id' => $candidate_id])->delete();
        //Candidate Languages English
        $candidatelanguage = new RecCandidateLanguage;
        $candidatelanguage->candidate_id = $candidate_id;
        $candidatelanguage->language_id = 1;
        $candidatelanguage->speaking = $request['speaking_english'];
        $candidatelanguage->reading = $request['reading_english'];
        $candidatelanguage->writing = $request['writing_english'];
        $candidate->languages()->save($candidatelanguage);

        //Candidate Languages French
        $candidatelanguage = new RecCandidateLanguage;
        $candidatelanguage->candidate_id = $candidate_id;
        $candidatelanguage->language_id = 2;
        $candidatelanguage->speaking = $request['speaking_french'];
        $candidatelanguage->reading = $request['reading_french'];
        $candidatelanguage->writing = $request['writing_french'];
        //$candidate = Candidate::find($candidate_id);
        $candidate->languages()->save($candidatelanguage);
    }

    /**
     * Store  Candidate Skills
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeSkills($request, $candidate_id)
    {
        if (isset($request['skills'])) {
            $skills = json_decode($request['skills']);
            $arr = array("word" => 1, "excel" => 2, "powerpoint" => 3, "customerservice" => 4, "leadership" => 5, "problemsolving" => 6, "timemanagement" => 7);
            RecCandidateSkill::where(['candidate_id' => $candidate_id])->delete();
            foreach ($skills[0] as $id => $skill) {
                $candidateskills = new RecCandidateSkill;
                $candidateskills->candidate_id = $candidate_id;
                $candidateskills->skill_id = $arr[$id];
                $candidateskills->skill_level = $skill;
                $candidateskills->save();
            }
        } else {
            $skills = $request['skill'];
            RecCandidateSkill::where(['candidate_id' => $candidate_id])->delete();
            foreach ($skills as $id => $skill) {
                $candidateskills = new RecCandidateSkill;
                $candidateskills->candidate_id = $candidate_id;
                $candidateskills->skill_id = $id;
                $candidateskills->skill_level = $skill;
                $candidateskills->save();
            }
        }
    }


    /**
     * Store Candidate Address
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */
    public function storeAddress($request, $candidate_id)
    {
        $addresses = json_decode($request['addresses']);
        foreach ($prev_address as $key => $value) {
            $data[$key]['address'] = $addresses['address'];
            $data[$key]['from'] = $prev_address_from['addressfrom'];
            $data[$key]['to'] = $prev_address_to['addressto'];
            $data[$key]['candidate_id'] = $candidate_id;
        }
        RecCandidateAddress::where(['candidate_id' => $candidate_id])->delete();
        RecCandidateAddress::insert($data);
    }

    /**
     * Store Candidate Job Details
     *
     * @param \Illuminate\Http\Request $request
     * @param   $candidate_id
     * @return \Illuminate\Http\Response
     */

    public function storeJob($request, $candidate_id)
    {

        $data['candidate_id'] = $candidate_id;
        $data['fit_assessment_why_apply_for_this_job'] = 'nil';
        $data['brand_awareness_id'] = $request['brand_awareness_id'];
        $data['security_awareness_id'] = $request['security_awareness_id'];
        $data['prefered_hours_per_week'] = $request['hours_per_week'];

        RecCandidateAwareness::updateOrCreate(
            array('candidate_id' => $candidate_id),
            //'job_id' => $session_obj['job']->id),
            $data
        );
    }

    /**
     * Store  Candidate Screening Questions
     *
     * @param \Illuminate\Http\ScreeningQuestionsRequest $request
     * @return json
     */
    public function store_screening_questions(RecScreeningQuestionsRequest $request)
    {

        try {
            DB::beginTransaction();
            $session_obj = $request->session()->get('CANINFO');
            RecCandidateScreeningQuestion::where(['candidate_id' => $session_obj['candidate']->id])->delete();
            $answers = $request->get('answer');
            $scores = $request->get('_sc');
            foreach ($answers as $id => $question) {
                $candidatescreening = new RecCandidateScreeningQuestion;
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
     * Store  Candidate Personality
     *
     * @param \Illuminate\Http\ScreeningQuestionsRequest $request
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
     * @param \Illuminate\Http\ScreeningQuestionsRequest $request
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

    /**
     * Get Other Languages
     *
     * @param Illuminate\Http\Request;     *
     * @return datatable object
     */
    public function getOtherlanguages(Request $request)
    {
        $languages = Languages::get();
        $id = $request->id;
        return view('hranalytics::job-application.partials.profile.language', compact('languages', 'id'));
    }
}
