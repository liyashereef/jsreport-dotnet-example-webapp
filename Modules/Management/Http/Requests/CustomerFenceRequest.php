<?php

namespace Modules\Management\Http\Requests;
use Modules\Admin\Http\Requests\CustomerRequest;


class CustomerFenceRequest extends CustomerRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $rules = [
           
            'fence_interval'=>'required_if:customer_type,==,1|integer|min:1',
            'contractual_visit_unit'=>'required_if:customer_type,==,1',

        ];
        return $rules;
    }

    public function messages()
    {
       
        $msg= [
            
            'fence_interval.required_if' => 'Fence interval is required',
            'contractual_visit_unit.required_if' => 'Contractual visit unit is required',
            'fence_interval.min' => 'Please choose number greater than 0',
        ];
        
        return $msg;
    }
}