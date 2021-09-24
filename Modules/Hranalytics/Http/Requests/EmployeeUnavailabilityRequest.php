<?php

namespace Modules\Hranalytics\Http\Requests;
use Modules\Hranalytics\Rules\DateExistRule;
use Modules\Timetracker\Models\EmployeeUnavailability;
class EmployeeUnavailabilityRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $start_date = request('from');
        $end_date = request('to');
        $employee_id = request('employee_id');
         $date_rules = [
                'from' => ['bail', 'required','date_format:"Y-m-d"','after:today', new DateExistRule($id, $start_date, $end_date,$employee_id)],
                'to' => ['bail', 'required', 'date_format:"Y-m-d"','after:today', 'after_or_equal:from',new DateExistRule($id, $start_date, $end_date,$employee_id)]
                ];    
        if (request('id')) {
            $details=EmployeeUnavailability::find($id);
            
            if(request('from') != $details->from)
            {
                $date_rules = [
                
                'from' => ['bail', 'required','date_format:"Y-m-d"','after:today', new DateExistRule($id, $start_date, $end_date,$employee_id)],
               ];
            }elseif(request('to') != $details->to){
                $date_rules = [
               
                'to' => ['bail', 'required', 'date_format:"Y-m-d"','after:today', 'after_or_equal:from',new DateExistRule($id, $start_date, $end_date,$employee_id)]
                ];    
            }
            else{
                
                $date_rules = [
                'from' => ['bail', 'required','date_format:"Y-m-d"', new DateExistRule($id, $start_date, $end_date,$employee_id)],
                'to' => ['bail', 'required', 'date_format:"Y-m-d"',new DateExistRule($id, $start_date, $end_date,$employee_id)]
                ];
            }
            
        }
        
        
        $rule = [
        
            'comments' => 'bail|required|max:500',
           
        ];
        $rules = array_merge($rule, $date_rules);
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
            'from.required' => 'Please enter the from date.',
            'to.required' => 'Please enter the to date.',
            'from.date_format' => 'Please enter the date in Y-m-d format.',
            'to.date_format' => 'Please enter the date in Y-m-d format.',
            'from.after' => 'From date must be a date after today.',
            'to.after' => 'To date must be a date after today.',
            'to.after_or_equal' => 'To date must be a date after or equal to the From date.',
            'comments.max'=>'Comments should not exceed 500 characters'
        ];
    }

}
