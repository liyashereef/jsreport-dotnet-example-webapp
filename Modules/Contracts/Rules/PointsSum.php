<?php

namespace Modules\Contracts\Rules;

use Illuminate\Contracts\Validation\Rule;

class PointsSum implements Rule
{
    protected $id;
    protected $criteria_name;
    protected $points;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id, $criteria_name, $points)
    {
        $this->criteria_name = $criteria_name;
        $this->points = $points;
        $this->id = $id;
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
        $result_arr = array();
        $expectedvalue = 0;
        for ($i = 0; $i < count($this->criteria_name); $i++) {
            if (array_key_exists($this->criteria_name[$i], $result_arr)) {
                $result_arr[$this->criteria_name[$i]] += $this->points[$i];
            } else {
                $result_arr[$this->criteria_name[$i]] = $this->points[$i];
            }
        }

        foreach ($result_arr as $key => $value) {
            $expectedvalue = $expectedvalue + $value;
        }
        if ($expectedvalue == 100) {
            $pass = true;
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
        return 'Sum of points of a criteria should be 100 ';
    }
}
