<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DistinctCaseInsensitive implements Rule
{

    protected $answer_option;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($answer_option)
    {
        $this->answer_option = $answer_option;
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
        $array_count = array_count_values(array_map('strtolower',$this->answer_option)); 
        //check whther array element doesnt exists multiple times 
        if ($array_count[strtolower($value)]>=2) {
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
        return 'This option already exists.';
    }
}
