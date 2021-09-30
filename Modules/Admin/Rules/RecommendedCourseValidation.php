<?php

namespace Modules\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;

class RecommendedCourseValidation implements Rule
{

    protected $mandatory_course;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($mandatory_course)
    {
        $this->mandatory_course = $mandatory_course;
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
        if (in_array($value, $this->mandatory_course)) {
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
        return 'Course already added as Mandatory';
    }
}