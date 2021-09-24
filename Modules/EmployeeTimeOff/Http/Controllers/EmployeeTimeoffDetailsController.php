<?php

namespace Modules\EmployeeTimeOff\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Services\HelperService;
use Modules\EmployeeTimeOff\Http\Requests\ApprovalTimeOffRequest;

use Modules\Admin\Repositories\UserRepository;
use Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffRepository;
use Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffWorkflowRepository;
use Modules\EmployeeTimeOff\Repositories\TimeOffLogRepository;

class EmployeeTimeoffDetailsController extends Controller
{

    /**
     * Repository instance.
     * @var \Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffRepository
     * @var \Modules\Admin\Repositories\UserRepository
     *
     */
    protected $employeeTimeoffRepository, $userRepository, $timeOffLogRepository, $employeeTimeoffWorkflowRepository;

    /**
     * Create Repository instance.
     * @param  \App\Repositories\CustomerRepository $customerRepository
     * @return void
     */
    public function __construct(EmployeeTimeoffRepository $employeeTimeoffRepository, UserRepository $userRepository, TimeOffLogRepository $timeOffLogRepository, EmployeeTimeoffWorkflowRepository $employeeTimeoffWorkflowRepository, HelperService $helperService)
    {
        $this->employeeTimeoffRepository = $employeeTimeoffRepository;
        $this->userRepository = $userRepository;
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
        return view('employeetimeoff::time-off-detail');
    }

    /**
     *  To display the leave details of all employees whose pending with the current logged in user
     * 
     */
    public function list()
    {
         $details = $this->employeeTimeoffRepository->list();

        return datatables()->of($details)->addIndexColumn()->toJson();
    }

    /***
     *  To display the leave details of an employee
     *  @param integer employee_id
     *  @return response
     */
    public function listSingle($employee_id,$request_type = null )
    {
         $request_type=request('type');
         $details = $this->employeeTimeoffRepository->listSingle($employee_id,$request_type);

        return datatables()->of($details)->addIndexColumn()->toJson();
    }


    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function approveOrReject(ApprovalTimeOffRequest $request)
    {
        //dd($request);
        try {
            \DB::beginTransaction();
            $input = $request->all();
            $employeeTimeOffLog = $this->timeOffLogRepository->store($input);
            $employeeTimeOffUpdate = $this->employeeTimeoffRepository->approveOrReject($employeeTimeOffLog);
            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse($e));
        }
    }

     /**
     * Get single record
     * @return Response
     */
    public function getSingle($id)
    {
        return  $this->employeeTimeoffRepository->getSingle($id);
    }    

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('employeetimeoff::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        return $request->all();
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('employeetimeoff::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('employeetimeoff::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
