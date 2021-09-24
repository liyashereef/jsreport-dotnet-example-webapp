<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;

class MinvalueGreaterthanMaxvalue implements Rule
{
    protected $min_value;
    protected $prev_max_value;
    protected $count;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($min_value, $prev_max_value, $count, $precision)
    {
        $this->min_value = $min_value;
        $this->prev_max_value = $prev_max_value;
        $this->count = $count;
        $this->precision = $precision;
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
        $precision = $this->precision;
        $min_value = round((float) $this->min_value, $precision);
        $prev_max_value = $this->prev_max_value;
        $count = $this->count;
        $step_counter = round((float) (pow(10, ($precision * -1))), $precision);
        $exact_next_value = round($prev_max_value, $precision) + $step_counter;
        if (round($min_value, $precision) == round($exact_next_value, $precision) || $count == 0) {
            return true;
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
        return 'Min value should be exactly the next value of previous max value.';
    }
}
