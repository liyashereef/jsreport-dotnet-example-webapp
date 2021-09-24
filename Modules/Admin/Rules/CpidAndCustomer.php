<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Admin\Repositories\CpidCustomerAllocationRepository;

class CpidAndCustomer implements Rule
{
    protected $customerId;
    protected $cpidCustomerAllocationRepo;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($customerId)    
    {
        $this->customerId = $customerId;
        $this->cpidCustomerAllocationRepo =  app()->make(CpidCustomerAllocationRepository::class);
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
        $cpc = $this->cpidCustomerAllocationRepo->getByCustomerIdCpid($this->customerId, $value);
        if ($cpc->isEmpty()) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid cpid customer type relation';
    }
}
