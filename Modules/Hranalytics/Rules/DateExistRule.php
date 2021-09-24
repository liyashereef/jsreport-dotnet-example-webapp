<?php

namespace Modules\Hranalytics\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Timetracker\Models\EmployeeUnavailability;
class DateExistRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $id;
    protected $startDate;
    protected $endDate;
    protected $empId;
    public function __construct($id, $startDate, $endDate, $empId)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->empId = $empId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $pass = false;
        $where_array = array();

         if ($this->startDate != null && $this->endDate != null && $this->empId != null) {
            $where_array = [['to', '>=', $this->startDate], ['from', '<=', $this->endDate], ['employee_id', '=', $this->empId]];
            $where_start_array = [['to', '>=', $this->startDate], ['from', '<=', $this->startDate], ['employee_id', '=', $this->empId]];
            $where_end_array = [['from', '<=', $this->endDate], ['to', '>=', $this->endDate], ['employee_id', '=', $this->empId]];

            if ($this->id != 0) {
                // Update case
                $not_id = ['id', '<>', $this->id];
                array_push($where_array, $not_id);
                array_push($where_start_array, $not_id);
                array_push($where_end_array, $not_id);
            }

            $date_overlap_count = EmployeeUnavailability::where($where_array)->count();
            if ($date_overlap_count != 0) {
                $overlapping_start_count = EmployeeUnavailability::where($where_start_array)->count();
                $overlapping_end_count = EmployeeUnavailability::where($where_end_array)->count();

                if ($attribute == 'to' && $overlapping_end_count == 0 && $overlapping_start_count > 0) {
                    // This should be caught at end date
                    $pass = true;
                }
                if ($attribute == 'from' && $overlapping_start_count == 0 && $overlapping_end_count > 0) {
                    // This should be caught at start date
                    $pass = true;
                }
                if ($overlapping_start_count == 0 && $overlapping_end_count == 0) {
                    $pass = false;
                }
            } else {
                $pass = true;
            }
         } else {
             $pass = true;
         }
        return $pass;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Date Already Exist';
    }
}
