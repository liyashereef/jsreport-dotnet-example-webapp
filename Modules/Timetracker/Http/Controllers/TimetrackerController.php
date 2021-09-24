<?php

namespace Modules\Timetracker\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Timetracker\Repositories\TimetrackerRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Timetracker\Repositories\CustomerQrcodeRepository;

class TimetrackerController extends Controller
{
    /**
     * The Repository instance.
     *
     * @var \Modules\Timetracker\Repositories\TimetrackerRepository
     */
    protected $timetrackerRepository, $customer_employee_allocation_repository,
        $customerReporsitory, $customerQrCodeRepository;

    /**
     * Create Repository instance.
     *
     * @param  \Modules\Timetracker\Repositories\TimetrackerRepository $timetrackerRepository
     * @return void
     */
    public function __construct(
        TimetrackerRepository $timetrackerRepository,
        CustomerEmployeeAllocationRepository $customer_employee_allocation_repository,
        CustomerRepository $customerReporsitory,
        UserRepository $userRepository,
        CustomerQrcodeRepository $customerQrCodeRepository
    ) {
        $this->customer_employee_allocation_repository = $customer_employee_allocation_repository;
        $this->timetrackerRepository = $timetrackerRepository;
        $this->customerReporsitory = $customerReporsitory;
        $this->userRepository = $userRepository;
        $this->customerQrCodeRepository = $customerQrCodeRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('timetracker::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('timetracker::create');
    }

    public function widgetDataEntry(Request $request)
    {
        $startDate = null;
        $endDate = null;
        if (!empty($request->get('start_date')) && !empty($request->get('end_date'))) {
            $startDate = \Carbon::parse($request->get('start_date'))->startOfDay();
            $endDate = \Carbon::parse($request->get('end_date'))->endOfDay();
        }
        $this->customerQrCodeRepository->widgetEntries($startDate, $endDate);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('timetracker::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('timetracker::edit');
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

    /**
     * Get Timesheet
     * @return view
     */
    public function reportTimeSheet()
    {
        //$employeeLookup = $this->userRepository->getUserLookup(null, ['admin', 'super_admin']);
        $allocated_customers_arr = $this->customer_employee_allocation_repository->getAllocatedCustomers(Auth::user());
        $employeeLookup =$this->customer_employee_allocation_repository->allocationList($allocated_customers_arr, null, ['admin', 'super_admin'])->pluck('full_name', 'id')->toArray();
        $customer_details_arr = $this->customerReporsitory->getCustomers($allocated_customers_arr);
        return view(
            'timetracker::timesheet',
            [
                'payperiod_list' => $this->timetrackerRepository->getAllPayperiods(),
                'current_payperiod' => $this->timetrackerRepository->getCurrentPayperiod(),
                'allocated_customers' =>  $customer_details_arr,
                'employeeLookupList' =>  $employeeLookup
            ]
        );
    }

    /**
     * Get Timesheet Report
     * @return json
     */
    public function getTimesheetReport(Request $request)
    {
        $current_user = Auth::user();
        $payperiod = $request->get('payperiod');
        $customer = $request->get('customer');
        $employee = $request->get('employee');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        return datatables()->of($this->timetrackerRepository
            ->timesheetReport($payperiod, $customer, $employee, $current_user, $customer_session = false, $fromDate, $toDate))->toJson();
    }

    /**
     * Get Timesheet Details
     * @return view
     */
    public function reportTimeSheetDetail()
    {
        return view('timetracker::timesheet-detail', ['payperiod_list' => $this->timetrackerRepository->getAllPayperiods(), 'current_payperiod' => $this->timetrackerRepository->getCurrentPayperiod()]);
    }

    /**
     * Get Timesheet Report Detail
     * @return json
     */
    public function getTimesheetReportDetail(Request $request)
    {
        $payperiod = $request->get('payperiod');
        $fromdate = $request->get('from_date');
        $todate = $request->get('to_date');
        // $payperiod=whereBetween(array($fromDate,$toDate));

        return datatables()->of($this->timetrackerRepository->timesheetReportDetail($payperiod, $fromdate, $todate))->toJson();
    }

    /**
     * Get Employee Summary
     * @return view
     */
    public function employeeSummary()
    {
        return view('timetracker::employee-summary', ['payperiod_list' => $this->timetrackerRepository->getAllPayperiods(), 'current_payperiod' => $this->timetrackerRepository->getCurrentPayperiod()]);
    }

    /**
     * Get Employee Summary Report
     * @return json
     */
    public function getEmployeeSummaryReport(Request $request)
    {
        $payperiod = $request->get('payperiod');
        $fromdate = $request->get('from_date');
        $todate = $request->get('to_date');
        return datatables()->of($this->timetrackerRepository->employeeSummaryReport($payperiod, $fromdate, $todate))->toJson();
    }

    /**
     * Get Employee Performance
     * @return view
     */
    public function employeePerformance()
    {
        return view('timetracker::employee-performance');
    }

    /**
     * Get Employee Performance Report
     * @return json
     */
    public function getEmployeePerformanceReport()
    {
        return datatables()->of($this->timetrackerRepository->employeePerformanceReport())->toJson();
    }

    /**
     * Get Allocation
     * @return view
     */
    public function allocation()
    {
        return view('timetracker::allocation', ['payperiod_list' => $this->timetrackerRepository->getAllPayperiods(), 'current_payperiod' => $this->timetrackerRepository->getCurrentPayperiod()]);
    }

    /**
     * Get Allocation Report
     * @return array
     */
    public function getAllocationReport(Request $request)
    {
        $payperiod = $request->get('payperiod');
        $fromdate = $request->get('from_date');
        $todate = $request->get('to_date');
        return datatables()->of($this->timetrackerRepository->allocationReport($payperiod, $fromdate, $todate))->make(true);
    }
}
