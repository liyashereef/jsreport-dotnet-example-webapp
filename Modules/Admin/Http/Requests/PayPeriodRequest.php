<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Rules\PayperiodDateOverlap;
use Modules\Admin\Rules\PayPeriodMiddleDateValidation;
use Modules\Admin\Rules\YearValidation;

class PayPeriodRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $start_date = request('start_date');
        $end_date = request('end_date');
        $week_one_end_date = request('week_one_end_date');
        $week_two_start_date = request('week_two_start_date');
        return $rules = [
            'year' => ['bail', 'required', new YearValidation],
            'pay_period_name' => "bail|required|max:255|unique:pay_periods,pay_period_name,{$id},id,deleted_at,NULL",
            'short_name' => "bail|required|max:50|unique:pay_periods,short_name,{$id},id,deleted_at,NULL",
            'start_date' => ['bail', 'required', 'date', new PayperiodDateOverlap($id, $start_date, $end_date)],
            'week_one_end_date' => ['bail', 'required', 'date', 'after:start_date','before:end_date'],
            'week_two_start_date' => ['bail', 'required', 'date','after:week_one_end_date','before:end_date', new PayPeriodMiddleDateValidation($id, $week_one_end_date, $week_two_start_date)],
            'end_date' => ['bail', 'required', 'date', 'after:start_date', new PayperiodDateOverlap($id, $start_date, $end_date)],
       
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'year.required' => 'Year is required.',
            'pay_period_name.required' => 'Pay period name is required.',
            'pay_period_name.unique' => 'This Pay period is already added.',
            'start_date.required' => 'Start Date is required.',
            'end_date.required' => 'End Date is required.',
        ];
    }

}
