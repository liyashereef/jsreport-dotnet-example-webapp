<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;

class Greaterthan implements Rule
{
    protected $min_value;
    protected $max_value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($min_value, $max_value)
    {
        $this->min_value = $min_value;
        $this->max_value = $max_value;
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
        $min_value = $this->min_value;
        $max_value = $this->max_value;
        if ($min_value < $max_value) {
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
        return 'Max value should be greater than min value.';
    }
}
