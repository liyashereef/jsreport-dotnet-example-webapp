<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Admin\Models\Customer;
use Modules\Timetracker\Models\WorkHourActivityCodeCustomers;

class ActivityTypeCustomerType implements Rule
{
    protected $customerId;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($customerId)
    {
        $this->customerId = $customerId;
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
        $customer = Customer::find($this->customerId);
        if ($customer != null) {
            $ct = $customer->customerType;
            if ($ct != null) {
                $res = WorkHourActivityCodeCustomers::where('work_hour_type_id', '=', $value)
                    ->where('customer_type_id','=',$ct->id)
                    ->get();
                if ($res->isNotEmpty()) {
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
        return 'Invalid activity type';
    }
}
