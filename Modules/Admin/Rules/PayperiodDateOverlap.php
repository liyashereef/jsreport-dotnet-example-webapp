<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Admin\Models\PayPeriod;

class PayperiodDateOverlap implements Rule
{
    protected $id;
    protected $startDate;
    protected $endDate;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id, $startDate, $endDate)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
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

        if ($this->startDate != null && $this->endDate != null) {
            $where_array = [['end_date', '>=', $this->startDate], ['start_date', '<=', $this->endDate], ['active', '=', 1]];
            $where_start_array = [['end_date', '>=', $this->startDate], ['start_date', '<=', $this->startDate], ['active', '=', 1]];
            $where_end_array = [['start_date', '<=', $this->endDate], ['end_date', '>=', $this->endDate], ['active', '=', 1]];

            if ($this->id != 0) {
                // Update case
                $not_id = ['id', '<>', $this->id];
                array_push($where_array, $not_id);
                array_push($where_start_array, $not_id);
                array_push($where_end_array, $not_id);
            }

            $date_overlap_count = PayPeriod::where($where_array)->count();
            if ($date_overlap_count != 0) {
                $overlapping_start_count = PayPeriod::where($where_start_array)->count();
                $overlapping_end_count = PayPeriod::where($where_end_array)->count();

                if ($attribute == 'end_date' && $overlapping_end_count == 0 && $overlapping_start_count > 0) {
                    // This should be caught at end date
                    $pass = true;
                }
                if ($attribute == 'start_date' && $overlapping_start_count == 0 && $overlapping_end_count > 0) {
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
        return 'Date Period is Overlapped';
    }
}
