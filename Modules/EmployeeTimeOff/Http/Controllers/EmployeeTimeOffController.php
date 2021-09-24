<?php

namespace Modules\EmployeeTimeOff\Http\Controllers;

use App\Services\HelperService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\EmployeeTimeOff\Http\Requests\EmployeeTimeOffRequest;
use Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffRepository;
use Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffWorkflowRepository;
use Modules\EmployeeTimeOff\Repositories\TimeOffLogRepository;

class EmployeeTimeOffController extends Controller
{

    /**
     * Repository instance.
     * @var \Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffRepository
     * @var \App\Services\HelperService
     */
    protected $employeeTimeoffRepository, $helperService;

    /**
     * Create Repository instance.
     * @param  \Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffRepository $employeeTimeoffRepository
     * @param  \Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffWorkflowRepository $employeeTimeoffWorkflowRepository
     * @param  \Modules\EmployeeTimeOff\Repositories\TimeOffLogRepository $timeOffLogRepository
     * @param  \App\Services\HelperService $helperService $helperService
     * @return void
     */
    public function __construct(EmployeeTimeoffWorkflowRepository $employeeTimeoffWorkflowRepository, TimeOffLogRepository $timeOffLogRepository, EmployeeTimeoffRepository $employeeTimeoffRepository, HelperService $helperService)
    {
        $this->employeeTimeoffRepository = $employeeTimeoffRepository;
        $this->timeOffLogRepository = $timeOffLogRepository;
        $this->employeeTimeoffWorkflowRepository = $employeeTimeoffWorkflowRepository;
        $this->helperService = $helperService;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $time_off_details = $this->employeeTimeoffRepository->getLookupList();
        $timeoff_data = $this->employeeTimeoffRepository->timeoffInitialArray();
        return view('employeetimeoff::time-off-request', ['employee_list' => $time_off_details['employee_list'], 'project_list' => $time_off_details['project_list'], 'logged_in_user' => $time_off_details['logged_in_user'], 'timestamp' => $time_off_details['timestamp'], 'request_type' => $time_off_details['request_type'], 'category' => $time_off_details['category'], 'leave_reason' => $time_off_details['leave_reason'], 'pay_period' => $time_off_details['pay_period'], 'oc_email' => $time_off_details['oc_email'], 'timeoff_data' => $timeoff_data]);
    }

    public function getCalculatedTimeoff($id)
    {
        return response()->json($this->timeoffData($id));
    }

    public function timeoffData($id)
    {
        $timeoff_data = $this->employeeTimeoffRepository->getTimeOff([$id], null, ['employee_id','customer_id','request_type_id']);
        if (empty($timeoff_data)) {
            $data = $this->employeeTimeoffRepository->timeoffInitialArray();
        } else {
            $data = $timeoff_data[0]['timeoff'];
        }

        return $data;
    }

    /**
     * Function to get the Employee leave list
     * @return array
     */
    function list() {
        return $details = $this->employeeTimeoffRepository->list();
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        //return view('employeetimeoff::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  EmployeeTimeOffRequest $request, $module
     * @return Response
     */
    public function store(EmployeeTimeOffRequest $request, $module)
    {
        try {
            \DB::beginTransaction();
            $employeeWorkflow = $this->employeeTimeoffWorkflowRepository->getRoleWorkflow($request->employee_role_id);
            $employeeTimeOffStore = $this->employeeTimeoffRepository->store($request, $module);
            $employeeTimeOffLog = $this->timeOffLogRepository->store($employeeTimeOffStore);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse($request->id));
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $time_off_details = $this->employeeTimeoffRepository->getLookupList($id);
        $timeoff_data = $this->timeoffData($time_off_details['time_off_edit_details']->employee_id);
        return view('employeetimeoff::time-off-request', ['employee_list' => $time_off_details['employee_list'], 'project_list' => $time_off_details['project_list'], 'logged_in_user' => $time_off_details['logged_in_user'], 'timestamp' => $time_off_details['timestamp'], 'request_type' => $time_off_details['request_type'], 'category' => $time_off_details['category'], 'leave_reason' => $time_off_details['leave_reason'], 'pay_period' => $time_off_details['pay_period'],
            'time_off_edit_details' => $time_off_details['time_off_edit_details'], 'employee' => $time_off_details['employee'],
            'supervisor' => $time_off_details['supervisor'], 'area_manager' => $time_off_details['area_manager'], 'hr' => $time_off_details['hr'],'oc_email' => $time_off_details['oc_email'],'timeoff_data' => $timeoff_data]);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        //return view('employeetimeoff::show');
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
