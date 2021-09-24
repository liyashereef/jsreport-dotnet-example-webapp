<?php

namespace Modules\ProjectManagement\Http\Requests;
use Modules\Admin\Rules\Greaterthan;


class RatingToleranceRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
         $array_size = sizeof(request('position'));
         $max_value = request('max_value');
         $min_value = request('min_value');
         $nrules = [];
         $rules = [];
          $distinct_rule= [
            'max_value.*' => 'bail|distinct',

        ];
         for ($i = 0; $i < $array_size-1; $i++) {
         
        

            $min_max_rules = [
                'max_value.' . $i => ['required','numeric','regex:/^\d+(\.\d{1,2})?$/','max:4.99',new Greaterthan($min_value[$i], $max_value[$i])],
                
            ];

            $nrules = array_merge($rules, $min_max_rules);
            $rules = array_merge($nrules, $distinct_rule);

        }
        return $rules;
        
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'max_value.*.required' => 'Maximum limit is required.',
            'max_value.*.numeric' => 'Maximum limit should be numeric.',
            'max_value.*.distinct' => 'Maximum limit should be distinct.',
            'max_value.*.regex' => 'This field should contain maximum 2 decimal points.',
            'max_value.*.max' => 'Maximum limit should be less than 5',
        ];
    }

}
