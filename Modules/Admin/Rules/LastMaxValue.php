<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;

class LastMaxValue implements Rule
{
    protected $i;
    protected $row_size;
    protected $template_max_value;
    protected $max_value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($i, $row_size, $template_max_value, $max_value)
    {
        $this->i = $i;
        $this->row_size = $row_size;
        $this->template_max_value = $template_max_value;
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
        $i = $this->i;
        $row_size = $this->row_size;
        $template_max_value = $this->template_max_value;
        $max_value = $this->max_value;
        if ($i == $row_size) {
            if ($max_value == $template_max_value) {
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
        $template_max_value = $this->template_max_value;
        return 'Max value should be equal to ' . $template_max_value;
    }
}
