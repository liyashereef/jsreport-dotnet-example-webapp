<?php

namespace Modules\Timetracker\Repositories;

use Modules\Timetracker\Models\EmployeeShiftWorkHourType;
use Modules\Timetracker\Models\WorkHourActivityCodeCustomer;
use Modules\Admin\Models\Customer;

class EmployeeShiftWorkHourTypeRepository
{
    protected $model;
    protected $workHourModel;

    public function __construct(EmployeeShiftWorkHourType $model, WorkHourActivityCodeCustomer $workHourModel)
    {
        $this->model = $model;
        $this->workHourModel = $workHourModel;
    }

    public function getAll()
    {
        return $this->model->orderByRaw('sort_order IS NULL, sort_order ASC')->get();
    }

    public function generateOptionsList($selected = null)
    {
        $workTypes = $this->model->all();
        $options = '';

        foreach ($workTypes as $workType) {
            $isSelected = ($workType->id === $selected) ? 'selected' : '';
            $options .= '<option value="' . $workType->id . '"' . $isSelected . '>' . $workType->name . '</option>';
        }
        return $options;
    }

    public function generateCustomerOptionsList($customer_id, $selected = null)
    {
        $customer = Customer::withTrashed()->find($customer_id);

        if ($customer->customer_type_id != null) {
            $workTypes = $this->workHourModel
                ->where("customer_type_id", $customer->customer_type_id)
                ->get()->sortBy('work_hour_type_trashed.sort_order', SORT_REGULAR, false);
            $options = '';
            $work_hour_type_id = [];
            foreach ($workTypes as $workType) {
                if (!in_array($workType->work_hour_type_id, $work_hour_type_id)) {
                    $isSelected = ($workType->work_hour_type_id === $selected) ? 'selected' : '';
                    $options .= '<option attr-order="' . $workType->work_hour_type_trashed->sort_order . '" value="' . $workType->work_hour_type_id . '"' . $isSelected . '>' . $workType->work_hour_type->name . '</option>';
                    array_push($work_hour_type_id, $workType->work_hour_type_id);
                }
            }
            return $options;
        } else {
            return null;
        }
    }
}
