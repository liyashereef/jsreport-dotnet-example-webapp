<?php

namespace Modules\Timetracker\Repositories;

use App\Services\HelperService;
use Modules\Admin\Models\CpidCustomerAllocations;
use Modules\Admin\Models\CpidLookup;
use Modules\Timetracker\Models\EmployeeShiftCpid;
use Modules\Timetracker\Models\EmployeeShiftPayperiod;
use Modules\Admin\Repositories\CpidLookupRepository;
use Modules\Admin\Repositories\CpidCustomerAllocationRepository;

class EmployeeShiftCpidRepository
{
    protected $model;
    protected $cpidLookupRepository;
    protected $cpidCustomerAllocationRepository;

    public function __construct(
        EmployeeShiftCpid $model,
        CpidLookupRepository $cpidLookupRepository,
        CpidCustomerAllocationRepository $cpidCustomerAllocationRepository
    ) {
        $this->model = $model;
        $this->cpidLookupRepository = $cpidLookupRepository;
        $this->CpidCustomerAllocationRepository = $cpidCustomerAllocationRepository;
        $this->helper_service = new HelperService();
    }

    public function getByEmployeeShiftPayperiodId($id)
    {
        return  $this->model->where('employee_shift_payperiod_id', '=', $id);
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
    private function calculateAmountBy($rate, $time)
    {
        $timeSeconds = $this->strTimeToSeconds($time);
        $timeHr = ($timeSeconds / (60 * 60));
        return ($rate * $timeHr);
    }
    /**
     * Create or update the cpid fields
     */
    public function store($inputs)
    {
        $total = 0;
        $esc = null;
        $datas = [];
        //on editing cpid
        if (isset($inputs['id'])) {
            $esc = $this->model->find($inputs['id'])->load('cpid_rates_with_trash');

            if ($esc->cpid == $inputs['cpid']) {
                $datas['rate_p_holiday']  = $esc->cpid_rates_with_trash->p_holiday;
                $datas['rate_p_overtime'] = $esc->cpid_rates_with_trash->p_overtime;
                $datas['rate_p_standard'] = $esc->cpid_rates_with_trash->p_standard;
            } else {
                $datas = $this->getEffectiveCpidRates($inputs['cpid']);
                $inputs['cpid_rate_id'] = $datas['cpid_rate_id'];
            }
        } else {
            //creating new cpid and rate
            $datas = $this->getEffectiveCpidRates($inputs['cpid']);
            $inputs['cpid_rate_id'] = $datas['cpid_rate_id'];
        }

        // if ($inputs['work_hour_type_id'] == 3) {
        //     $total = $this->calculateAmountBy($datas['rate_p_holiday'], $inputs['hours']);
        // } elseif ($inputs['work_hour_type_id'] == 2) {
        //     $total = $this->calculateAmountBy($datas['rate_p_overtime'], $inputs['hours']);
        // } else {
        //     $total = $this->calculateAmountBy($datas['rate_p_standard'], $inputs['hours']);
        // }
        $total = $this->calculateAmountBy($datas['rate_p_standard'], $inputs['hours']);

        $inputs['total_amount'] = $total;

        if (is_object($esc)) {
            //todo ::reate
            unset($inputs['id']);
            $esc->update($inputs);
        } else {
            return $this->model->create($inputs);
        }
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

    public function deleteByEmpoyeeIdAndShiftId($inputs, $notInArray)
    {
        $result =  $this->model->where('employee_id', $inputs['employee_id'])
            ->where('employee_shift_payperiod_id', $inputs['employee_shift_payperiod_id'])
            ->whereNotIn('id', $notInArray)->delete();
    }

    public function getAllBy($filter = null)
    {
        return $this->model
            ->where(function ($query) use ($filter) {
                if (isset($filter['employee_shift_payperiod_id']) && !empty($filter['employee_shift_payperiod_id'])) {
                    $query->where('employee_shift_payperiod_id', $filter['employee_shift_payperiod_id']);
                }
            })->with('cpid_lookup')
            ->get();
    }

    /**
     * selected = selecte value |cpid or null
     */
    public function getCpidSelectOptions($customerId, $empShiftDetailId, $selected = null)
    {
        //load basic objects
        $cpidCustomerAllocationRepository = app()->make(CpidCustomerAllocationRepository::class);
        $empShiftDetail = EmployeeShiftPayperiod::find($empShiftDetailId);
        $allocatedCpids = $cpidCustomerAllocationRepository->getByCustomerIdWithActive($customerId)
            ->pluck('cpid')
            ->toArray();

        //init  basic variables
        $isSelectedDefault = '';
        $defaultOptionString = '';
        $options = '';
        //dd($allocatedCpids);

        //if the cpid is selected / in edit mode
        if ($selected) {
            $isSelectedDefault = 'selected';
            // array_push($allocatedCpids, $selected);
            $allocatedCpids = array_unique($allocatedCpids);
        }

        //set default position as selected
        $defaultUserPositionName = isset($empShiftDetail->trashed_user->trashedEmployee->employeePosition->position)
            ? $empShiftDetail->trashed_user->trashedEmployee->employeePosition->position
            : '';

        //process all CPIDS
        foreach (CpidLookup::withTrashed()->findMany($allocatedCpids) as $cpidLookup) {
            $isOptionSelected = '';
            $cpidLookup->load('position'); //load relations on the fly

            //set values to postion string.
            $positionString = isset($cpidLookup->position->position) ? $cpidLookup->position->position : '';
            //Set option selected attribute
            if (!empty($selected) && $selected === $cpidLookup->id) {
                $isOptionSelected = 'selected';
            }
            //if the position string is the default user role name set it as defaut
            if ($defaultUserPositionName === $positionString) {
                $defaultOptionString .= '<option value=' . $cpidLookup->id .
                    ' data-role-name="' . $positionString . '"' . $isOptionSelected  . '>'
                    . $cpidLookup->cpid . '</option>';
                continue;
            }
            //Normal dropdown options
            $options .= ('<option value=' . $cpidLookup->id .
                ' data-role-name="' . $positionString . '"' . $isOptionSelected . '>'
                . $cpidLookup->cpid . '</option>');
        }

        return  $defaultOptionString . $options;
    }

    /**
     * Get Time Widget Data For FM Dashboard.
     * @parameters $pay_periods(based on start and end date), and $customer_id
     * @responce
     */
    public function getPositionWiseNetTime($pay_periods)
    {

        $inputs = $this->helper_service->getFMDashboardFilters();
        $inputs['pay_periods'] = $pay_periods;
        return $this->model //->whereIn('employee_shift_payperiod_id',$pay_periods)
        ->whereHas('cpid_customer_allocation', function ($query) use ($inputs) {
            if (!empty($inputs)) {
                //For customer_ids
                $query->whereIn('customer_id', $inputs['customer_ids']);
            }
        })
            ->whereHas('employee_shift_payperiod', function ($q) use ($inputs) {
                $q->whereIn('pay_period_id', $inputs['pay_periods']);
                $q->whereIn('customer_id', $inputs['customer_ids']);
            })
            ->groupBy('cpid')
            ->select('cpid', \DB::raw('SUM(TIME_TO_SEC(`hours`)) as total_hours'))
            ->with('cpid_lookup', 'cpid_lookup.position')
            ->with('cpid_lookup_with_trash', 'cpid_lookup_with_trash.position')
            ->get();
    }

    /**
     * Get Time Widget Data For FM Dashboard.
     * @parameters $pay_periods(based on start and end date), and $customer_id
     * @responce
     */
    public function getTimesheetReconciliationData($inputs)
    {

        return $this->model //->whereIn('employee_shift_payperiod_id',$inputs['pay_periods'])
        /* ->whereHas('cpid_customer_allocation', function ($query) use ($inputs) {
            if (!empty($inputs)) {
                //For customer_ids
                if (!empty($inputs['customer_id'])) {
                    $query->where('customer_id', $inputs['customer_id']);
                }
            }
        }) */
        ->whereHas('employee_shift_payperiod', function ($q) use ($inputs) {
            $q->whereIn('pay_period_id', $inputs['pay_periods']);
            $q->where('customer_id', $inputs['customer_id']);
        })
            ->groupBy('cpid', 'work_hour_type_id', 'cpid_rate_id', 'employee_shift_payperiod_id')
            ->select('cpid', 'work_hour_type_id', 'cpid_rate_id', 'employee_shift_payperiod_id', \DB::raw('SUM(TIME_TO_SEC(`hours`)) as total_hours'), \DB::raw('SUM(total_amount) as total_pay'))
            ->with('cpid_lookup', 'cpid_lookup.position', 'cpid_rates_with_trash', 'shift_work_hour_type', 'employee_shift_payperiod')
            ->with('cpid_lookup_with_trash', 'cpid_lookup_with_trash.position', 'cpid_rates_with_trash', 'shift_work_hour_type', 'employee_shift_payperiod', 'employee_shift_payperiod.trashed_employee.trashed_employee_position')
            ->get();
    }
}
