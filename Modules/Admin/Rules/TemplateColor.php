<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;

class TemplateColor implements Rule
{
    protected $rule_colors;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($rule_colors)
    {
        $this->rule_colors = $rule_colors;

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
        $rule_colors = $this->rule_colors;
        $count = array_count_values($rule_colors);
        if ($value != null) {
            if ($count[$value] > 1) {
                return false;
            }
            return true;
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
        return 'Color Already Chosen';
    }
}
