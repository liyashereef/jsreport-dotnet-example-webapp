<?php

namespace Modules\Reports\Repositories;

use DB;
use Carbon;
use Modules\Client\Models\VisitorLogDetails;
use Modules\Admin\Repositories\CustomerEmployeeAllocationRepository;
use Modules\Admin\Repositories\CustomerRepository;
use Auth;


class VisitorLogReportRepository
{   
    protected $visitorLogDetails, $customerEmployeeAllocationRepository, $customerRepository;

    public function __construct(
        VisitorLogDetails $visitorLogDetails,
        CustomerEmployeeAllocationRepository $customerEmployeeAllocationRepository,
        CustomerRepository $customerRepository

    ) {
        $this->visitorLogDetails = $visitorLogDetails;
        $this->customerEmployeeAllocationRepository = $customerEmployeeAllocationRepository;
        $this->customerRepository = $customerRepository;

    }

    public function getCustomerList() {
        if (\Auth::user()->can('view_visitor_log_report')) {
                $user = Auth::User();
                $customerAllocatedList = $this->customerEmployeeAllocationRepository->getAllocatedCustomersList($user);
                return $customerAllocatedList;
        }
    }

    public function getVisitorLogDetails($data) {
        $end = Carbon::parse($data->endDate)->addDays(1)->format('Y-m-d');
        
        $visitorData = VisitorLogDetails::with('type', 'customer')
            ->whereBetween('checkin',[$data->startDate, $end])
            ->where('customer_id', $data->site)
            ->select(DB::raw('DATE(checkin) as date'),'visitor_type_id', DB::raw('COUNT(visitor_type_id) as total_visitors'))
            ->groupBy('date', 'visitor_type_id')
            ->get();
        
        $prevDate = -1;
        $visitorDetail = [];

        foreach ($visitorData as $key => $value) {
            if ($value->date != $prevDate) {
                $prevDate = Carbon::parse($value->date)->toFormattedDateString();
            }
            $visitorDetail[$prevDate][$value->type->type] = $value->total_visitors;
        }

        foreach ($visitorDetail as $key => $value) {
            if (!(array_key_exists("Visitor",$visitorDetail[$key]))) {
                $visitorDetail[$key]['Visitor'] = 0;
            }
            if (!(array_key_exists("Employee",$visitorDetail[$key]))) {
                $visitorDetail[$key]['Employee'] = 0;
            }
            if (!(array_key_exists("Contractor",$visitorDetail[$key]))) {
                $visitorDetail[$key]['Contractor'] = 0;
            }
        }

        foreach ($visitorDetail as $key => $value) {
            ksort($visitorDetail[$key]);
        }

        $dates = array_keys($visitorDetail);
        $visitor = array();
        $employee = array();
        $contractor = array();

        foreach ($visitorDetail as $key => $value) {
            if (array_key_exists("Visitor",$visitorDetail[$key])) {
                array_push($visitor,$visitorDetail[$key]['Visitor']);
            }
            if (array_key_exists("Employee",$visitorDetail[$key])) {
                array_push($employee,$visitorDetail[$key]['Employee']);
            }
            if (array_key_exists("Contractor",$visitorDetail[$key])) {
                array_push($contractor,$visitorDetail[$key]['Contractor']);
            }
        }

        return ['dates' => $dates, 'contractor' => $contractor, 'employee' => $employee, 'visitor' => $visitor];
        
    }

   
}