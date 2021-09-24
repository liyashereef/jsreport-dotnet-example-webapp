<?php

namespace Modules\Timetracker\Http\Controllers;

use Modules\Timetracker\Repositories\EmployeeTimeoffRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Customer;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\LeaveReasonRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Repositories\CpidLookupRepository;
use Carbon\Carbon;
use Modules\Timetracker\Models\EmployeeTimeoff;
use Modules\Admin\Models\Employee;
use Session;

class EmployeeTimeoffController extends Controller {

    protected $employeeTimeoffRepository, $customeremployeeallocationrepository, $customerrepository, $leaveReasonRepository, $userRepository, $cpidLookupRepository;

    public function __construct(EmployeeTimeoffRepository $employeeTimeoffRepository, CustomerRepository $customerrepository, LeaveReasonRepository $leaveReasonRepository, UserRepository $userRepository, CpidLookupRepository $cpidLookupRepository) {
        $this->employeeTimeoffRepository = $employeeTimeoffRepository;
        $this->customerrepository = $customerrepository;
        $this->leaveReasonRepository = $leaveReasonRepository;
        $this->cpidLookupRepository = $cpidLookupRepository;
        $this->userRepository = $userRepository;
        $this->middleware(function ($request, $next) {
            $request->session()->forget('timeoff_requestid');
            $request->session()->forget('timeoff_customer');
            $request->session()->forget('timeoff_customerstc');
            $request->session()->forget('timeoff_startdate');
            $request->session()->forget('timeoff_starttime');
            $request->session()->forget('timeoff_enddate');
            $request->session()->forget('timeoff_endtime');
            $request->session()->forget('timeoff_payrate');
            $request->session()->forget('timeoff_employeename');
            $request->session()->forget('timeoff_formattedstartdate');
            $request->session()->forget('timeoff_formattedenddate');
            
            return $next($request);

        });
        
        
    }

    /**
     * Display default page.
     * @return Response
     */
    public function index(Request $request) {
        $request->session()->forget('timeoff_requestid');
        $request->session()->forget('timeoff_customer');
        $request->session()->forget('timeoff_customerstc');
        $request->session()->forget('timeoff_startdate');
        $request->session()->forget('timeoff_starttime');
        $request->session()->forget('timeoff_enddate');
        $request->session()->forget('timeoff_endtime');
        $request->session()->forget('timeoff_payrate');
        $request->session()->forget('timeoff_employeename');
        $request->session()->forget('timeoff_formattedstartdate');
        $request->session()->forget('timeoff_formattedenddate');
        $this->cpidLookupRepository->getAllCpidByParameters(23, false);
        return view('timetracker::timeoff');
    }

    /**
     * getting timeoff requests
     * @return Response
     */
    public function getTimeoffRequests() {
        return datatables()->of($this->employeeTimeoffRepository->getTimeoffRequests())->addIndexColumn()->toJson();
    }

    /**
     * getting timeoff request form
     * @return Response
     */
    public function showTimeoffRequestForm() {
        $customerlist = $this->customerrepository->getCustomerList();
        $role = ['guard', 'MST GUARD'];
        $emp = $this->userRepository->getUserLookup($role,['admin','super_admin']);
        $flipped = array_flip($emp);
        $sortkey = ksort($flipped);
        $employeelist = array_flip($flipped);

        //dd($employeelist);
        // $employeelist = $this->userRepository->getUserLookup(null,["super_admin","admin","client"]);
        $employeelistbackup = Employee::all();
        $employeeIdList = array();
        //[TODO] THIS SHOULD BE CHANGED - TEMP SOLUTION
        foreach($employeelistbackup as $eachEmployee) {
            if($eachEmployee->id != 0 && $eachEmployee->id != 1 && $eachEmployee->employee_no != '') {
                $employeeIdList[$eachEmployee->id] = $eachEmployee->employee_no.' - '.$eachEmployee->user->first_name.' '.$eachEmployee->user->last_name;
            }            
        }
        $reasonlist = $this->leaveReasonRepository->getAll();
        //$employeelist = $employeeIdList;
        return view('timetracker::timeoff-request-form', compact('customerlist', 'employeelist', 'reasonlist'));
    }

    public function backFillprocess(Request $request) {
        $customer_id = $request->customer_id;
        $requirement_id = $request->requirement_id;
        $customer_details = Customer::find($customer_id);
        $timeoff_details = EmployeeTimeoff::select('*',
        \DB::raw('(select p_standard from cpid_rates where id=employee_timeoff.cpidRate_id) as cpidpayrate'),
        \DB::raw('(select concat_ws(" " ,first_name,last_name) from users where id=employee_timeoff.employee_id) as employeename'))
        ->with(['user','reasons','customer','employee','employee.trashedUser','cpidRate'])
        ->find($requirement_id);
       
        $timeoffformattedstartdate = date("l, F d, Y",strtotime($timeoff_details->start_date));
        $timeoffformattedenddate = date("l, F d, Y",strtotime($timeoff_details->end_date));
        
        $request->session()->put('timeoff_requestid',$timeoff_details->id);
        $request->session()->put('timeoff_customer',$timeoff_details->customer->id);
        $request->session()->put('timeoff_customerstc',$timeoff_details->customer->stc);
        $request->session()->put('timeoff_startdate',$timeoff_details->start_date);
        $request->session()->put('timeoff_starttime',$timeoff_details->start_time);
        $request->session()->put('timeoff_enddate',$timeoff_details->end_date);
        $request->session()->put('timeoff_endtime',$timeoff_details->end_time);
        $request->session()->put('timeoff_payrate',$timeoff_details->cpidpayrate);
        $request->session()->put('timeoff_employeename',$timeoff_details->user->first_name . " ".$timeoff_details->user->last_name);
        $request->session()->put('timeoff_formattedstartdate',$timeoffformattedstartdate);
        $request->session()->put('timeoff_formattedenddate',$timeoffformattedenddate);
        return redirect('/hranalytics/candidate/schedule/customer/'.$timeoff_details->customer->id);


    }

    public function storeTimeoffRequestForm(Request $request) {

        $projectId = $request->project_id;
        $customer = Customer::find($projectId);

        $result['project_number'] = $customer->project_number;
        $result['project_id'] = $request->project_id;
        $result['employee_id'] = $request->employee_id;
        $result['cpidRate_id'] = $request->pay_rate;
        $result['start_date'] = $request->start_date;
        $result['start_time'] = $request->start_time;
        $result['end_date'] = $request->end_date;
        $result['end_time'] = $request->end_time;
        $result['reason_id'] = $request->reason_id;
        $result['backfillstatus'] = 0;
        $result['created_by'] = Auth()->user()->id;

        try {
            $resp = $this->employeeTimeoffRepository->saveEmployeeTimeOff($result);
            return $resp;
        } catch (Throwable $th) {
            return $th;
        }
    }

    public function payRollList(Request $request) {
        
        $projectId = $request->project_id;
        //$employeeId = $request->employee_id;
        //dd($projectId);
        //dd($employeeId);

        $data = $this->cpidLookupRepository->getAllCpidByParameters($projectId);
        return $data;
    }

}
