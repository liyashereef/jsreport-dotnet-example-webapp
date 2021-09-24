<?php

namespace Modules\Timetracker\Repositories;

use App\Services\HelperService;
use DB;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Repositories\CpidLookupRepository;
use Modules\Timetracker\Models\EmployeeShiftReportEntry;
use Modules\Uniform\Repositories\UraTransactionRepository;

class ManualTimesheetEntryRepository
{

    protected $cpidLookupRepository, $employeeShiftCpidRepository, $employeeShiftReportEntry, $helperService;
    protected $uraTransactionRepository;

    public function __construct(
        CpidLookupRepository $cpidLookupRepository,
        EmployeeShiftCpidRepository $employeeShiftCpidRepository,
        EmployeeShiftReportEntry $employeeShiftReportEntry,
        UraTransactionRepository $uraTransactionRepository,
        HelperService $helperService
    ) {
        $this->cpidLookupRepository = $cpidLookupRepository;
        $this->employeeShiftCpidRepository = $employeeShiftCpidRepository;
        $this->employeeShiftReportEntry = $employeeShiftReportEntry;
        $this->uraTransactionRepository = $uraTransactionRepository;
        $this->helperService = $helperService;
    }

    /**
     * Store to Employee shift report entries
     * @param Request
     * @return Bool
     */
    public function storeEntry($data)
    {

        $user = $data->employee;
        for ($i = 0; $i < count($user); $i++) {
            $each = [];
            $each['payperiod_id'] = $data->payperiod_id;
            $each['payperiod_week'] = $data->payperiod_week;
            $each['user_id'] = $user[$i];
            $each['customer_id'] = $data->customer_id;

            // verify rate
            $cpidRate = $this->employeeShiftCpidRepository->getEffectiveCpidRates($data->cpid[$i]);
            $each['cpid_rate_id'] = $cpidRate['cpid_rate_id'];

            // verify function id
            $cpidFunction = $this->cpidLookupRepository->get($data->cpid[$i]);
            $each['cpid_function_id'] = $cpidFunction->cpid_function_id;

            $each['work_hour_type_id'] = $data->work_hour_type[$i];
            $each['work_hour_activity_code_customer_id'] = $data->activity_code[$i];

            // total amount calculation
            $hoursAndTotalRate = $this->totalAmountCalculation($data->hours[$i], $cpidRate['rate_p_standard']);
            $each['hours'] = $hoursAndTotalRate['totalMin'];
            $each['total_amount'] = $hoursAndTotalRate['totalAmount'];
            $each['created_by'] = Auth::user()->id;

            $entry = $this->employeeShiftReportEntry->create($each);
            $this->uraTransactionRepository->processTimesheetApproval($entry);
        }

        return true;
    }

    public function totalAmountCalculation($hour, $rate)
    {
        $totalMin = $this->helperService->h2m($hour);
        $totalHour = $totalMin / 60;
        $totalAmount = $totalHour * $rate;
        return ['totalMin' => $totalMin, 'totalAmount' => $totalAmount];
    }

    public function getEmployeeTimesheetApproval($payperiod, $week, $user)
    {
        $userHistory = EmployeeShiftReportEntry::select('customer_id', DB::raw('sum(hours) as total_hours'), 'created_by')
            ->where('payperiod_id', $payperiod)
            ->where('payperiod_week', $week)
            ->where('user_id', $user)
            ->groupBy('customer_id', 'created_by')
            ->with('customer', 'createdBy')
            ->get();

        return $this->prepareEmployeeHistory($userHistory);
    }

    public function prepareEmployeeHistory($history)
    {
        $dataHistory = [];
        $aggregateHour = 0;
        if ($history !== null)
            foreach ($history as $key => $value) {
                $data['project_no'] = $value->customer->project_number;
                $data['client_name'] = $value->customer->client_name;
                $data['total_hours'] = (int) $value->total_hours;
                $data['approved_by'] = isset($value->createdBy) ? $value->createdBy->full_name : '';
                array_push($dataHistory, $data);
            }

        if (null !== $dataHistory) {
            $hr = data_get($dataHistory, '*.total_hours');
            $aggregateHour = array_sum($hr);
        }

        return ['history' => $dataHistory, 'totalHour' => $aggregateHour];
    }
}
