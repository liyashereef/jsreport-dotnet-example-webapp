<?php

namespace Modules\Expense\Http\Requests;


class MileageReimbursementRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
  
    public function rules()
    {
        $mileage_type = request('mileage-reimbursement-type');
        //dd(request('starting_kilometer'));
        if($mileage_type == 0){
            $rules = [
                'flat_rate' => ['bail', 'required', 'numeric', 'regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/', 'between:0,999']
            ];
        } else {
            $rules = [
                'starting_kilometer.*' =>  "bail|required|numeric|digits_between:1,6",
                'ending_kilometer.*' => "bail|required|numeric|digits_between:1,6|gte:starting_kilometer.*",
                'cost.*' => "bail|required|numeric",
            ];
        }
        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function  messages()
    {
        return [
            'flat_rate.required' => 'Flat Rate is required.',
            'flat_rate.regex' => 'Flat Rate should have maximum 2 decimals',
            'flat_rate.between' => 'Flat Rate should be maximum 3 digits',
            'starting_kilometer.*.required' => 'Starting Kilometer is required.',
            'ending_kilometer.*.required' => 'Ending Kilometer is required.',
            'ending_kilometer.*.gte' =>'Ending Kilometer should be greater than Starting Kilometer.',
            'starting_kilometer.*.digits_between'=>'Starting kilometer should be between 1 and 6 digits',
            'ending_kilometer.*.digits_between'=>'Ending kilometer should be between 1 and 6 digits',
            'cost.*.required' => 'Cost is required.',
            'starting_kilometer.*.numeric' => 'Starting Kilometer is only accepted numbers.',
            'ending_kilometer.*.numeric' => 'Ending Kilometer is only accepted numbers.',
            'cost.*.numeric' => 'Cost is only accepted numbers.',

        ];
    }

}