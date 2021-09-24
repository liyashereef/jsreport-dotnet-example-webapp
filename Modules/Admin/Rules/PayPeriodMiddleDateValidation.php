<?php

namespace Modules\Admin\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class PayPeriodMiddleDateValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id,$endDate,$startDate)
    {
        //
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

        $to = strtotime($this->endDate);
        $from = strtotime($this->startDate);
        if((($from-$to)/(60*60*24)) == 1)
        {
            return true;
        }else{
            return false;
        }

            

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Week two start date should be the next day of week one end date';
    }
}
