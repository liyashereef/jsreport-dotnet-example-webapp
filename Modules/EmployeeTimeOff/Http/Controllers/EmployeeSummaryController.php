<?php

namespace Modules\EmployeeTimeOff\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\EmployeeAllocationRepository;
use Modules\Admin\Repositories\TimeOffRequestTypeLookupRepository;
use Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffRepository;

class EmployeeSummaryController extends Controller
{

    protected $employeeTimeoffRepository, $timeOffRequestTypeLookupRepository, $employeeAllocationRepository, $customerEmployeeAllocation;

    /**
     * Repository instance.
     * @var \Modules\EmployeeTimeOff\Repositories\EmployeeTimeoffRepository
     * @var \Modules\Admin\Repositories\TimeOffRequestTypeLookupRepository
     *
     */
    public function __construct(EmployeeTimeoffRepository $employeeTimeoffRepository, TimeOffRequestTypeLookupRepository $timeOffRequestTypeLookupRepository, EmployeeAllocationRepository $employeeAllocationRepository, CustomerEmployeeAllocationRepository $customerEmployeeAllocation)
    {
        $this->employeeTimeoffRepository = $employeeTimeoffRepository;
        $this->timeOffRequestTypeLookupRepository = $timeOffRequestTypeLookupRepository;
        $this->employeeAllocationRepository = $employeeAllocationRepository;
        $this->customerEmployeeAllocation = $customerEmployeeAllocation;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requestType = $this->timeOffRequestTypeLookupRepository->getLookupList();
        $count = $this->timeOffRequestTypeLookupRepository->getAll()->count();
        return view('employeetimeoff::time-off-summary', compact('requestType', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    /**
     * Display a listing of the timeoff based on their type.
     * @param $type
     */
    public function summaryList()
    {
        if (\Auth::user()->can('view_employee_summary_all')) {
            $data = $this->employeeTimeoffRepository->getTimeOff();
        } else {
            $allocated_user = $this->employeeAllocationRepository->getEmployeeIdAssigned(\Auth::user()->id)->toArray();
            $customers_list = $this->customerEmployeeAllocation->getAllocatedCustomers(\Auth::user(), false);
            $data = $this->employeeTimeoffRepository->getTimeOff($allocated_user, $customers_list);
        }
        return datatables()->of($data)->addIndexColumn()->toJson();
    }
}
