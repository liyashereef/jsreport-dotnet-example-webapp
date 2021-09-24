<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;

class RecommendedUniqueValidation implements Rule
{

    protected $recommended_course;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($recommended_course)
    {
        $this->recommended_course = $recommended_course;
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
        $recommended_course_array = array_count_values(array_filter($this->recommended_course));
        if ($recommended_course_array[$value] > 1) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Course already added';
    }
}
