<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;

class FirstMinValue implements Rule
{
    protected $i;
    protected $template_min_value;
    protected $min_value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($i, $template_min_value, $min_value)
    {
        $this->i = $i;
        $this->template_min_value = $template_min_value;
        $this->min_value = $min_value;
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
        $i = $this->i;
        $template_min_value = $this->template_min_value;
        $min_value = $this->min_value;
        if ($i == 0) {
            if ($template_min_value == $min_value) {
                return true;
            } else {
                return false;
            }
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
        $template_min_value = $this->template_min_value;
        return 'Min value should be equal to ' . $template_min_value;
    }
}
