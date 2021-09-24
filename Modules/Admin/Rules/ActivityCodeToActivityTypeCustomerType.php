<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Admin\Models\Customer;
use Modules\Timetracker\Models\WorkHourActivityCodeCustomers;

class ActivityCodeToActivityTypeCustomerType implements Rule
{
    protected $customerId;
    protected $workHrType;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($customerId, $workHrType)
    {
        $this->customerId = $customerId;
        $this->workHrType = $workHrType;
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
        $atIndex = explode('.', $attribute)[1];
        $customer = Customer::find($this->customerId);
        if ($customer != null) {
            $ct = $customer->customerType;
            if ($ct != null) {

                $res = WorkHourActivityCodeCustomers::where('id', '=', (int)$this->workHrType[$atIndex])
                    ->where('customer_type_id','=',$ct->id)
                    ->get()->count();

                if ($res > 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid activity code';
    }
}
