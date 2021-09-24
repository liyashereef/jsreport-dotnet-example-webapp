<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HelperService;
use DB;
use Modules\Admin\Models\TimesheetApprovalRatingConfiguration;
use Illuminate\Http\Request;
use Modules\Admin\Http\Requests\TimesheetApprovalConfigurationRequest;
use Modules\Admin\Repositories\TimesheetConfigurationRepository;
use Modules\Admin\Models\TimesheetApprovalConfiguration;

class TimesheetConfigurationController extends Controller
{
    protected $helperService;

    /**
     * Create Repository instance.
     *
     * @param  \App\Services\HelperService $helperService
     * @return void
     */
    public function __construct(HelperService $helperService,TimesheetConfigurationRepository $timesheetConfigurationRepository,TimesheetApprovalConfiguration $timesheetApprovalConfiguration)
    {
        $this->helperService = $helperService;
        $this->timesheetConfigurationRepository= $timesheetConfigurationRepository;
        $this->model = $timesheetApprovalConfiguration;

    }

    /**
     * Display a listing of the CustomerShift Types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ratingData = TimesheetApprovalRatingConfiguration::orderBy('id', 'asc')->get();
        $data = $this->model
        ->select('*',
        \DB::raw("TIME_FORMAT(time, '%h:%i %p') as time")
         )->first();
         $rows=[1,2,3];
         foreach($rows as $i){
             if($data['email_'.$i.'_time'] != null){
                if (substr($data['email_'.$i.'_time'], strpos($data['email_'.$i.'_time'], ".") + 1) == '0') {
                    $data['email_'.$i.'_time'] = intval($data['email_'.$i.'_time']);
                }
             }
         }
         foreach($ratingData as $rating){
            if (substr($rating['early'], strpos($rating['early'], ".") + 1) == '0') {
                $rating['early'] = intval($rating['early']);
            }
            if (substr($rating['untill'], strpos($rating['untill'], ".") + 1) == '0') {
                $rating['untill'] = intval($rating['untill']);
            }
        }
        $hour=['0','1','2','3','4','5','6','7','8','9','10','11','12'];
        return view('admin::timesheet-approval-configuration.timesheet-configuration',compact('data','hour','ratingData'));

    }

    public function getList()
    {
        return datatables()->of($this->timesheetConfigurationRepository->getAll())->addIndexColumn()->toJson();

    }



    public function store(TimesheetApprovalConfigurationRequest $request)
    {

        try {
            DB::beginTransaction();
            $this->timesheetConfigurationRepository->save($request->all());
            DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }





}
