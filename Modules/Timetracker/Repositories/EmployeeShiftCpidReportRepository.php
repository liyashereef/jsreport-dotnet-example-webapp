<?php

namespace Modules\Timetracker\Repositories;

use App\Services\HelperService;
use Modules\Admin\Models\CpidCustomerAllocations;
use Modules\Admin\Models\CpidLookup;
use Modules\Admin\Models\CpidRates;
use Modules\Timetracker\Models\EmployeeShiftCpid;
use Modules\Timetracker\Models\EmployeeShiftReportEntry;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Admin\Repositories\CpidLookupRepository;
use Modules\Admin\Repositories\CpidCustomerAllocationRepository;
use Modules\Uniform\Repositories\UraTransactionRepository;

class EmployeeShiftCpidReportRepository
{
    protected $model;
    protected $cpidLookupRepository;
    protected $cpidCustomerAllocationRepository;
    protected $uraTransactionRepository;

    public function __construct(
        EmployeeShiftReportEntry $model,
        CpidLookupRepository $cpidLookupRepository,
        CpidCustomerAllocationRepository $cpidCustomerAllocationRepository,
        UraTransactionRepository $uraTransactionRepository
    ) {
        $this->model = $model;
        $this->cpidLookupRepository = $cpidLookupRepository;
        $this->CpidCustomerAllocationRepository = $cpidCustomerAllocationRepository;
        $this->uraTransactionRepository = $uraTransactionRepository;
        $this->helper_service = new HelperService();
    }


    public function getEffectiveCpidRates($cpid_id)
    {
        $inputs = [];
        $cpids = $this->cpidLookupRepository->get($cpid_id);
        //get effective cpid rate
        $effectiveDate = $cpids->effectiveDate;
        if (is_object($effectiveDate)) {
            //todo::implement cpid will not store.
            $inputs['rate_p_holiday']  =  $effectiveDate->p_holiday;
            $inputs['rate_p_overtime'] = $effectiveDate->p_overtime;
            $inputs['rate_p_standard'] =  $effectiveDate->p_standard;
            $inputs['cpid_rate_id'] = $effectiveDate->id;
        }

        return $inputs;
    }

    private function calculateAmountBy($rate, $time)
    {
        $timeSeconds = $this->strTimeToSeconds($time);
        $timeHr = ($timeSeconds / (60 * 60));
        return ($rate * $timeHr);
    }


    private function strTimeToSeconds($time)
    {
        $secTime = 0;
        $arr = explode(':', $time);
        if ($arr >= 2) {
            $secTime += ((int) $arr[1] * 60);
            $secTime += ((int) $arr[0] * 3600);
        }
        return $secTime;
    }

    private function strTimeToMinutes($time)
    {
        $secTime = 0;
        $arr = explode(':', $time);
        return ($arr[0] * 60) + ($arr[1]);
    }

    /**
     * Create or update the cpid fields
     */
    public function store($cpid, $inputs)
    {
        $result = null;
        $total = 0;
        $esc = null;
        $datas = [];
        //on editing cpid
        $esc = CpidRates::where("cp_id", $cpid)->first();
        $datas['rate_p_holiday']  = $esc->p_holiday;
        $datas['rate_p_overtime'] = $esc->p_overtime;
        $datas['rate_p_standard'] = $esc->p_standard;
        $inputs['cpid_rate_id'] = $esc->id;


        // if ($inputs['work_hour_type_id'] == 3) {
        //     $total = $this->calculateAmountBy($datas['rate_p_holiday'], $inputs['hours']);
        // } elseif ($inputs['work_hour_type_id'] == 2) {
        //     $total = $this->calculateAmountBy($datas['rate_p_overtime'], $inputs['hours']);
        // } else {
        //     $total = $this->calculateAmountBy($datas['rate_p_standard'], $inputs['hours']);
        // }
        $total = $this->calculateAmountBy($datas['rate_p_standard'], $inputs['hours']);

        $inputs['total_amount'] = $total;
        $inputs['hours'] = $this->strTimeToMinutes($inputs['hours']);
        if ($this->model->where($inputs)->count() > 0) {
            $result = $this->model->create($inputs);
        } else {
            // $this->model->where([
            //     "payperiod_id" => $inputs["cpid_rate_id"],
            //     "payperiod_week" => $inputs["cpid_rate_id"],
            //     "user_id" => $inputs["cpid_rate_id"],
            //     "shift_payperiod_id" => $inputs["cpid_rate_id"],
            //     "customer_id" => $inputs["cpid_rate_id"],
            // ])->delete();
            $result = $this->model->create($inputs);
        }
        //Process ura transaction
        $this->uraTransactionRepository->processTimesheetApproval($result);
        return $result;
    }
}
