<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Admin\Models\ShiftModule;
use Modules\Reports\Repositories\CovidReportRepository;
use Modules\Employeescheduling\Repositories\SchedulingRepository;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Models\User;
use Modules\Admin\Models\Customer;
use Modules\Admin\Models\CustomerEmployeeAllocation;
use Modules\Admin\Repositories\CustomerRepository;

class CovidReportController extends Controller
{
    protected $covidReportRepository;
    protected  $SchedulingRepository;
    protected $customerEmployeeAllocationRepository;
    public function __construct(
        CovidReportRepository $covidReportRepository,
        CustomerRepository $customerRepository,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository
    ) {
        $this->covidReportRepository = $covidReportRepository;
        $this->customerRepository = $customerRepository;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
    }

    public function covidReport()
    {
        return view('reports::covidreport.covidreport');
    }

    public function getCovidReport(Request $request)
    {
        return datatables()->of($this->covidReportRepository->getCovidReport(
            $request->get("startDate"),
            $request->get("endDate"),
            $request->get("customer_id"),
            $request->get("area_manager"),
            $request->get("employees"),
            true
        ))->toJson();
    }
    public function getDailyTransactions(Request $request)
    {
        $adminRoles = User::select('id')
            ->role(['admin', 'super_admin'])->get()->pluck('id')->toArray();
        if (\Auth::user()->hasAnyPermission("super_admin", "admin")) {
            $Customers = $this->customerRepository->getCustomerList('ALL_CUSTOMER');
            $Customers = collect($Customers)->sortBy('project_number')->toArray();

            $customerAllocated = Customer::all()->pluck("id")->toArray();
            $Employees = User::whereNotIn("id", $adminRoles)->orderBy("first_name", "asc")->get();
        } else {
            $customerAllocated = CustomerEmployeeAllocation::where("user_id", \Auth::user()->id)
                ->get()->pluck("customer_id")->toArray();
            $Customers = Customer::select('id', 'project_number', 'client_name')->whereIn("id", $customerAllocated)
                ->get()->toArray();
            $Employees = $this->customerEmployeeAllocationRepository
                ->allocationList($customerAllocated);
        }

        $areaManager = [];
        $Supervisors = [];

        $areaManagerArray = User::select('id', 'first_name', 'last_name')
            ->permission(['area_manager'])->where('active', true)->orderBy('first_name')->get();
        $i = 0;
        foreach ($areaManagerArray as $areaManagers) {
            $areaManagerId = $areaManagers->id;
            if (!in_array($areaManagerId, $adminRoles)) {
                $areaManager[$i] = [$areaManagers->id, $areaManagers->getFullNameAttribute()];
                $i++;
            } else {
            }
        }
        return view('reports::covidreport.covid-daily-report', compact("Customers", "areaManager", "Employees"));
    }

    public function getFeverComplianceReport(Request $request)
    {

        $adminRoles = User::select('id')
            ->role(['admin', 'super_admin'])->get()->pluck('id')->toArray();
        if (\Auth::user()->hasAnyPermission("super_admin", "admin")) {
            $Customers = $this->customerRepository->getCustomerList('ALL_CUSTOMER');
            $Customers = collect($Customers)->sortBy('project_number')->toArray();

            $customerAllocated = Customer::all()->pluck("id")->toArray();
            $Employees = User::whereNotIn("id", $adminRoles)->orderBy("first_name", "asc")->get();
        } else {
            $customerAllocated = CustomerEmployeeAllocation::where("user_id", \Auth::user()->id)
                ->get()->pluck("customer_id")->toArray();
            $Customers = Customer::select('id', 'project_number', 'client_name')->whereIn("id", $customerAllocated)
                ->get()->toArray();
            $Employees = $this->customerEmployeeAllocationRepository
                ->allocationList($customerAllocated);
        }

        $areaManager = [];
        $Supervisors = [];

        $areaManagerArray = User::select('id', 'first_name', 'last_name')
            ->permission(['area_manager'])->where('active', true)->orderBy('first_name')->get();
        $i = 0;
        foreach ($areaManagerArray as $areaManagers) {
            $areaManagerId = $areaManagers->id;
            if (!in_array($areaManagerId, $adminRoles)) {
                $areaManager[$i] = [$areaManagers->id, $areaManagers->getFullNameAttribute()];
                $i++;
            } else {
            }
        }
        return view('reports::covidreport.covid-compliance-report', compact("Customers", "areaManager", "Employees"));
    }
    public function complianceReport()
    {
        return view();
    }

    public function getComplianceReport(Request $request)
    {
        $graphData = $this->covidReportRepository->getCovidGraphReport($request);
        return $graphData;
    }
}
