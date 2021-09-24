<?php

namespace Modules\Contracts\Http\Controllers;

use App\Repositories\MailQueueRepository;
use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Models\RfpAwardDateLookups;
use Modules\Admin\Models\RfpProcessStepLookups;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\ClientOnboardingTemplateRepository;
use Modules\Admin\Repositories\RfpResponseTypeLookupRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Contracts\Http\Requests\RfpDetailsRequest;
use Modules\Contracts\Models\RfpDetails;
use Modules\Contracts\Models\RfpDetailsWinLose;
use Modules\Contracts\Models\RfpTrackingStage;
use Modules\Contracts\Repositories\ClientOnboardingRepository;
use Modules\Contracts\Repositories\RfpRepository;
use Modules\Supervisorpanel\Repositories\CustomerMapRepository;
use Modules\Supervisorpanel\Repositories\CustomerReportRepository;
use mysql_xdevapi\Exception;
use View;

class RfpController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    protected $rfpRepository;
    protected $helperService;
    protected $userRepository;
    protected $customer_report_repository;
    protected $customer_map_repository;
    protected $rfpProcessstepLookups;
    protected $mailQueueRepository;
    protected $rfpResponseTypeLookupRepository;
    protected $clientOnboardingTemplate;

    public function __construct(RfpRepository $rfpRepository,
                                HelperService $helperService,
                                UserRepository $userRepository,
                                CustomerReportRepository $customer_report_repository,
                                CustomerMapRepository $customer_map_repository,
                                RfpProcessStepLookups $rfpProcessstepLookups,
                                MailQueueRepository $mailQueueRepository,
                                RfpResponseTypeLookupRepository $rfpResponseTypeLookupRepository,
                                ClientOnboardingTemplateRepository $clientOnboardingTemplate,
                                ClientOnboardingRepository $clientOnboarding
    )
    {
        $this->rfpRepository = $rfpRepository;
        $this->helperService = $helperService;
        $this->userRepository = $userRepository;
        $this->customer_report_repository = $customer_report_repository;
        $this->customer_map_repository = $customer_map_repository;
        $this->rfpProcessstepLookups = $rfpProcessstepLookups;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->rfpResponseTypeLookupRepository = $rfpResponseTypeLookupRepository;
        $this->clientOnboardingTemplate = $clientOnboardingTemplate;
        $this->clientOnboarding = $clientOnboarding;

    }

    //RFP CREATE
    public function rfpCreate()
    {
        $rfpLookups = $this->rfpRepository->getUserLookupByPermission(['rfp_respondent']);
        return view('contracts::rfp.list', compact('rfpLookups'));
    }

    //List
    public function getList()
    {
        $content =$this->rfpRepository->getAll();
        return datatables()->of($content)->addIndexColumn()->toJson();
    }

    public function trackrfp($rfp_id)
    {
        $rfpDetails = $this->rfpRepository->getRfpDetails($rfp_id);
        $lookups = $this->rfpProcessstepLookups->orderBy('step_number', 'ASC')->get();
        $user = \Auth::user();
        $users = $this->rfpRepository->getUserLookupByPermission(['rfp_respondent']);

        $already_processed_track_ids = array();

        $trackings = RfpTrackingStage::where(['rfp_details_id' => $rfp_id])->whereHas('tracking_process')->get();

        if (isset($trackings)) {
            foreach ($trackings as $each_track) {
                $already_processed_track_ids[$each_track->rfp_process_steps_id] = $each_track;
            }

        }
        return view('contracts::rfp.index', compact('lookups', 'users', 'user', 'already_processed_track_ids', 'trackings', 'rfpDetails'));
    }

    /**
     * @param $rfp_id
     * @param null $client_onboarding_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createClientOnboarding($rfp_id, $client_onboarding_id = null)
    {
        $rfpDetails = $this->rfpRepository->getRfpDetails($rfp_id);
        if(strtolower($this->rfpRepository->getRfpWinLose($rfp_id)->last()->status) !== "win") {
            throw new \Exception("Invalid data");
        }
        $onBoardingDetails = $this->clientOnboarding->getByRfpId($rfp_id)->first();
        if (isset($onBoardingDetails->id) && !isset($client_onboarding_id)) {
            return redirect()->route('rfp.create-client-onboarding', [$rfp_id, $onBoardingDetails->id]);
        }

        if (isset($client_onboarding_id)) {
            $sections = $this->clientOnboarding->getSectionWithTaskByOnboarding($client_onboarding_id)->toArray();
        } else {
            $sections = $this->clientOnboardingTemplate->getSectionWithTask()->toArray();
        }
        $sections = base64_encode(json_encode($sections));
        $user = \Auth::user();
        $employeeLookup = $this->userRepository
            ->getUserLookup();
        return view(
            'contracts::rfp.client-onboarding-steps',
            compact(
                'user',
                'sections',
                'rfpDetails',
                'employeeLookup',
                'onBoardingDetails'
            )
        );
    }

    public function trackClientOnboarding($rfp_id)
    {
        $this->clientOnboarding->sendNotification();
        $rfpDetails = $this->rfpRepository->getRfpDetails($rfp_id);
        $onBoardingDetails = $this->clientOnboarding->getByRfpId($rfp_id)->first();

        $percentageStepArr = range(0, 100, 5);
        $percentArray = array_combine($percentageStepArr, $percentageStepArr);
        return view('contracts::rfp.track-onboarding',
            compact('onBoardingDetails', 'rfpDetails', 'percentArray'));
    }

    public function storeClientOnboarding($rfp_id, $onboard_id = null)
    {
        try {
            \DB::beginTransaction();
            if ($onboard_id) {
                $this->clientOnboarding->updatePercentageCompleted();
            } else {
                $this->clientOnboarding->storeClientOnboarding();
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    public function trackRfpStore($rfp_id, Request $request)
    {
        return $this->rfpRepository->saveTrackingStep($rfp_id, $request);
    }

    public function index()
    {
        return view('contracts::index');
    }

    public function onboardingTargetDateRemainder()
    {
        try {
            \DB::beginTransaction();
            $result = $this->clientOnboarding->sendNotification();
            \DB::commit();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error("Onboarding Mail Error: ".$e->getMessage().' at '.$e->getLine().' in '.$e->getFile());
            return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $rfpDetails = null;
        $employeeLookup = $this->userRepository->getUserLookup(null,['admin','super_admin']);
        $employeeLookup[''] = 'Please Select';
        $rfpResponseTypeLookup = $this->rfpResponseTypeLookupRepository->getList();
        return view('contracts::rfp.create',
            compact('employeeLookup', 'rfpDetails', 'rfpResponseTypeLookup')
        );
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function storeStatus(Request $request)
    {
        try {
            \DB::beginTransaction();
            $lookup = $this->rfpRepository->saveStatus($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function rfpdestroy(Request $request)
    {
        $id = $request->id;
        $deletemodel = $this->rfpRepository->destroyRfp($id);
        if($deletemodel){
            return response()->json(["success"=>true]);
        }else{
            return response()->json(["success"=>false]);
        }
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(RfpDetailsRequest $request)
    {
        try {
            \DB::beginTransaction();
            if (isset($request->id)) {
                $rfp_details_id = $this->rfpRepository->storeRfp($request);
            } else {
                $rfp_details_id = $this->rfpRepository->storeRfp($request);
                $unique_key = $this->rfpRepository->getUniqueRFPKey($request, $rfp_details_id);
                $result = $this->rfpRepository->updateUniqueRFPKey($unique_key, $rfp_details_id);

                $rfp_details_win_loses = new RfpDetailsWinLose;
                $rfp_details_win_loses->rfp_details_id = $rfp_details_id;
                $rfp_details_win_loses->save();

                $users = User::where('id', $request->employee_id)->first();

                $estimatedAwardDate = $request->estimated_award_date;
                //here admin will give 10 days gap or 5 days its editable in rfp_award_dates
                $awardDateGap = RfpAwardDateLookups::first();
                $awardDate = $awardDateGap->award_dates;
                $currentDate = \Carbon::now();
                $dt = $currentDate->toDateString();

                $newAddedDate = date('Y-m-d', strtotime($estimatedAwardDate . ' - ' . $awardDate . 'days'));

                $to = $users->email;
                $cc = 'ben@dispostable.com';
                $mail_time = $newAddedDate;
                $user = \Auth::user();
                $created_by = $user->id;
                $model_name = 'Rfp';
                $subject = 'Update RFP Status';
                $message = 'Hi ' . $users->first_name . ',  Please update your RFP status Win/Lose.';

                // $mail_queue = $this->mailQueueRepository->storeMail($to,$cc,null,$subject,$message,$model_name,null,null,null,$mail_time);
                $mail_queue = $this->mailQueueRepository->storeMail($to, $subject, $message, $model_name);
            }
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            throw $e;
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */

    public function edit($id)
    {
        $rfpDetails = $this->rfpRepository->getForm($id);

        $employeeLookup = $this->userRepository->getUserLookup(null,['admin','super_admin']);
        $rfpResponseTypeLookup = $this->rfpResponseTypeLookupRepository->getList();
        return view('contracts::rfp.create',
            compact('employeeLookup', 'rfpDetails', 'rfpResponseTypeLookup'));
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Response
     */

    /**
     * Remove the specified resource from storage.
     * @return Response
     */

    public function rfpLink($stc = null, Request $request)
    {
        $postalCode = $request->all();
        $latest_template = $this->customer_report_repository->getLatestTemplate();
        $customers_arr_per = $this->customer_map_repository->getCustomerMapDetails($latest_template, $stc, $request);

        $customers_arr_temp = $this->customer_map_repository->getCustomerMapDetails($latest_template, $stc = 'stc', $request);

        $customers_arr = array_merge_recursive($customers_arr_temp, $customers_arr_per);

        $customer_score = $customers_arr['customer_score'];
        return view('contracts::rfp.rfplink', compact('postalCode', 'customer_score'));
    }

    public function storeWinLoseStatus(Request $request)
    {

        try {
            \DB::beginTransaction();
            $rfpWinLose = $this->rfpRepository->updateStatus($request->all());
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    public function rfpTrackingRemove($lookup_id, $rfp_id)
    {
        return $this->rfpRepository->deleteRfpTracking($lookup_id, $rfp_id);
    }

    public function getWinlosedetails(Request $request)
    {
        $id = $request->rfpid;
        $rfpwinlose = RfpDetailsWinLose::
        select('rfp_details_id', 'status', 'rfp_debrief_attended', 'rfp_debrief_attended_no', 'did_we_take_it', 'did_we_take_it_no', 'offered_by_the_client_no')
            ->where("rfp_details_id", $id)->first()->toArray();
        //->pluck('rfp_details_id','status','rfp_debrief_attended','rfp_debrief_attended_no','did_we_take_it','did_we_take_it_no','offered_by_the_client_no');
        return json_encode($rfpwinlose, true);
    }

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('edit-settings', function ($user) {
            return $user->isAdmin;
        });

        Gate::define('update-post', function ($user, $post) {
            return $user->id === $post->user_id;
        });
    }
}
