<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UniformSchedulingOfficeTimingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $id = request('id');
        return $rules = [
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'intervals' => 'required|numeric',
            'start_date' => 'required|date|after_or_equal:today',
            'expiry_date' => 'nullable|date|after_or_equal:start_date',
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
            'start_time.required' => 'Start time is required.',
            'end_time.required' => 'End time is required.',
            'end_time.after' => 'End time should be greater than start time.',
            'intervals.required' => 'Intervals is required.',
            'start_date.required' => 'Start date is required.',
            'expiry_date.after' => 'End date should be greater than start date.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}