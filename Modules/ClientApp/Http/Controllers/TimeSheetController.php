<?php

namespace Modules\ClientApp\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Repositories\PayPeriodRepository;
use Modules\ClientApp\Http\Resources\V1\TimeSheet\EmployeeShiftPayperiodResource;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;

class TimeSheetController extends Controller
{

    protected $payPeriodRepository;

    public function __construct(PayPeriodRepository $payPeriodRepository)
    {
        $this->payPeriodRepository = $payPeriodRepository;
    }

    public function timesheet(Request $request)
    {
        if (($request->has('payPeriodStart') && !empty($request->payPeriodStart)) || ($request->has('payPeriodEnd') && !empty($request->payPeriodEnd))) {
            $request->validate([
                'customerId' => 'required',
                'payPeriodStart' => 'required',
                'payPeriodEnd' => 'required',
            ]);
            $dateFilterApplied = true;
        } else {
            $request->validate([
                'customerId' => 'required',
            ]);
            $dateFilterApplied = false;
        }

        try {
            $customerId = $request->customerId;
            $payPeriodStart = null;
            $payPeriodEnd = null;
            if ($request->has('payPeriodStart') && !empty($request->payPeriodStart)) {
                $payPeriodStart = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($request->payPeriodStart)));
                $payPeriodStart->setTime(0, 0, 0);
            }

            if ($request->has('payPeriodEnd') && !empty($request->payPeriodEnd)) {
                $payPeriodEnd = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($request->payPeriodEnd)));
                $payPeriodEnd->setTime(23, 59, 59);
            }

            $timeSheetQuery = EmployeeShiftPayperiod::where('customer_id', $customerId)->where('active', true);
            if ($dateFilterApplied) {
                $payPeriods = $this->payPeriodRepository->getAllActivePayPeriodsBetweenDates($payPeriodStart, $payPeriodEnd);
                $timeSheetQuery->whereIn('pay_period_id', $payPeriods);
            }
            $timeSheetQuery->orderBy('created_at', 'desc');
            return EmployeeShiftPayperiodResource::collection($timeSheetQuery->get());
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
