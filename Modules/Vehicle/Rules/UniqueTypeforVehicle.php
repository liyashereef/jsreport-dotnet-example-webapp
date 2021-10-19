<?php

namespace Modules\Vehicle\Rules;

use Illuminate\Contracts\Validation\Rule;

class UniqueTypeforVehicle implements Rule
{
    protected $i;
    protected $template_min_value;
    protected $min_value;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct( $type)
    {

        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {     $flag=true;
           $count = array_count_values($this->type);
         if (in_array($value, $this->type) &&  $count[$value]>1 ) {
            $flag=false;
        } else {
            $flag=true;
        }
        return $flag;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Service type already exists for this vehicle';
    }
}
