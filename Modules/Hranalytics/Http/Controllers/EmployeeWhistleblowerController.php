<?php

namespace Modules\Hranalytics\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\Employee;
use Modules\Hranalytics\Repositories\EmployeeWhistleblowerRepository;
use Modules\Hranalytics\Models\EmployeeWhistleblower;
use Modules\Admin\Models\EmployeeWhistleblowerCategories;
use Modules\Admin\Models\EmployeeWhistleblowerPriorities;
use Modules\Admin\Repositories\UserRepository;
use Modules\Hranalytics\Http\Requests\EmployeeWhistleblowerRequest;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Auth;
use Modules\Admin\Models\EmployeeRatingPolicies;
use Modules\Admin\Models\WhistleblowerStatusLookup;
use Modules\Admin\Repositories\CustomerRepository;
class EmployeeWhistleblowerController extends Controller
{
    protected $whistleblowerRepository,
    $whistleblowerCategoryModel,
    $whistleblowerProrityModel,
    $employeeModel,
    $userRepository,
    $employeeAllocationrepository
    ;


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(
        EmployeeWhistleblowerRepository $whistleblowerRepository,
        UserRepository $userRepository,
        EmployeeWhistleblowerCategories $whistleblowerCategoryModel,
        EmployeeWhistleblowerPriorities $whistleblowerProrityModel,
        Employee $employeeModel,
        EmployeeAllocationRepository $employeeAllocationrepository,
        EmployeeRatingPolicies $employeeRatingPolicies,
        WhistleblowerStatusLookup $whistleblowerStatusLookup,
        CustomerRepository $customerRepository
        )
    {
        $this->repository = $whistleblowerRepository;
        $this->userrepository = $userRepository;
        $this->categories = $whistleblowerCategoryModel;
        $this->priorities = $whistleblowerProrityModel;
        $this->employees  = $employeeModel;
        $this->employeeAllocationRepository = $employeeAllocationrepository;
        $this->employeeRatingPolicies = $employeeRatingPolicies;
        $this->whistleblowerStatusLookup = $whistleblowerStatusLookup;
        $this->customerRepository = $customerRepository;
    }
    public function index()
    {
        $default      = ['null' =>'Please Select'];
        $current_date = Carbon::now();
        $user = \Auth::user();
        $employees = array();
        if($user->can('create_all_whistleblower') || $user->hasAnyPermission(['admin', 'super_admin'])){
          $employees = $this->repository->getAllEmployees($user->id);
        }elseif(
            !($user->can('create_all_whistleblower')) && !($user->can('create_allocated_whistleblower')) &&
            !($user->can('view_all_whistleblower')) && !($user->can('view_allocated_whistleblower'))
            ){

        $employees =   $this->repository->getcreatedEmployees($user->id);
        }
        else{

            $employees =   $this->repository->getAllocatedEmployees($user->id);
        }
        asort($employees);
        $employeelist = $default + $employees;
        if (\Auth::user()->can('create_all_whistleblower')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('all');
        } else if (\Auth::user()->can('create_allocated_whistleblower')) {
            $project_list = $this->customerRepository->getProjectsDropdownList('allocated');
        } else {
            $project_list = [];
        }
        $categorylist =  $default + $this->categories->pluck('roles', 'id')->toArray();
        $policylist = $default + $this->employeeRatingPolicies->pluck('policy','id')->toArray();
        $prioritylist =  $default + $this->priorities->orderBy('rank')->pluck('priority', 'id')->toArray();
        $statusList= $this->whistleblowerStatusLookup->orderBy('status')->get();
        return view('hranalytics::whistleblower.index',compact('project_list','current_date','statusList','categorylist', 'policylist','prioritylist','employeelist'));
    }

    public function getemployeelist($employess){
        $each_row = array();
        foreach($employess as $key => $each_list){
            $each_row["employee_no"]     = $each_list->employee_no;
            $each_row["employee_name"]   =  $each_list->user->first_name.' '.$each_list->user->last_name;

        }
        return  $each_row;
    }

    public function getEmployeeWhistleblowerSummaryList()
    {
        $user = \Auth::user();
        if($user->can('view_all_whistleblower') || $user->hasAnyPermission(['admin', 'super_admin'])){
            return datatables()->of($this->repository->getEmployeeWhistleblowersList())->addIndexColumn()->toJson();
        }
        elseif(($user->can('view_allocated_whistleblower')) && !($user->can('view_all_whistleblower')) && !($user->can('view_employee_whistleblower'))){
            return datatables()->of($this->repository->getAllocattedEmployeeWhistleblowerList())->addIndexColumn()->toJson();
        }
        else{
            return datatables()->of($this->repository->getCreatedEmployeeWhistleblowerList())->addIndexColumn()->toJson();
        }
    }
    public function store(EmployeeWhistleblowerRequest $request)
    {
        return $this->repository->store($request);
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        return $this->repository->getSingle($id);
        //return view('client::edit');
    }


    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function getLookups()
    {

    }
}
