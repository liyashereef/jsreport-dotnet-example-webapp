<?php

namespace Modules\ProjectManagement\Rules;

use Illuminate\Contracts\Validation\Rule;

class SumOfFields implements Rule
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
    public function __construct($deadline_weightage, $value_add_weightage, $initiative_weightage, $commitment_weightage, $complexity_weightage, $efficiency_weightage)
    {
        $this->deadline_weightage = $deadline_weightage;
        $this->value_add_weightage = $value_add_weightage;
        $this->initiative_weightage = $initiative_weightage;
        $this->commitment_weightage = $commitment_weightage;
        $this->complexity_weightage = $complexity_weightage;
        $this->efficiency_weightage = $efficiency_weightage;
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
        if ($this->deadline_weightage+$this->value_add_weightage+$this->initiative_weightage+$this->commitment_weightage+$this->complexity_weightage+$this->efficiency_weightage!=100) {
            $pass=false;
        } else {
            $pass=true;
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
        return 'Sum of all weightage should be equal to 100';
    }
}
