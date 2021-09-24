<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\ThresholdRequest;
use Modules\Admin\Repositories\ThresholdRepository;
use  Modules\Admin\Models\ScheduleSettings;

class ThresholdController extends Controller
{

    protected $helperService;
    /**
     * Display a listing of the resource.
     * @return Response
     */
     /**
     * Create Repository instance.
     * @param  \App\Modules\Admin\Repositories\ThresholdRepository $ThresholdRepository
     * @return void
     */
    public function __construct(HelperService $helperService, ThresholdRepository $ThresholdRepository)
    {
        $this->helperService = $helperService;
        $this->ThresholdRepository = $ThresholdRepository;
    }
    /**
     * Display a listing of the resource.
     * @return view
     */
    public function index()
    {  
        $scheduleSettingsData= $this->ThresholdRepository->getScheduleSettingsData();
        return view('admin::masters.threshold',compact("scheduleSettingsData")); 
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {   
        $scheduleSettingsData= $this->ThresholdRepository->getScheduleSettingsData();
        if(empty($scheduleSettingsData)) {
            $scheduleSettingsData = new ScheduleSettings();
        }
        $scheduleSettingsData->weekly_threshold=$request->customer;
        $scheduleSettingsData->bi_weekly_threshold=$request->threshold;
        $status = $this->ThresholdRepository->saveScheduleSettings($scheduleSettingsData);
        if($status!=null){
            $message='Threshold timeperiod has been successfully created.';
        }
        return response()->json(array('success' => true, 'message'=>$message, 'status' => $status));
       
    }
}
