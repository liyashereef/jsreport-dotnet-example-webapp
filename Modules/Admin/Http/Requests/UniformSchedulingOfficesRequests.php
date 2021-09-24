<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UniformSchedulingOfficesRequests extends FormRequest
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
            'name' => ['required'],
            'adress' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'phone_number' => 'required|max:13',
            'phone_number_ext' => 'nullable|numeric|digits_between:1,255',
            'office_start_time'=> 'required',
            'office_end_time'=> 'required'
        ];

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
            'name.required' => 'Name is required.',
            'adress.required' => 'Adress is required.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number_ext.required' => 'Phone number ext is required.',
            'phone_number_ext.numeric' => 'Phone number ext should not be in characters.',
            'phone_number_ext.digits_between' => 'Phone number ext must be between 1 and 255 digits.',
            'start_time.required' => 'Start time is required.',
            'end_time.required' => 'End time is required.',
            'end_time.after' => 'End time should be greater than start time.',
            'intervals.required' => 'Intervals is required.',
            'office_start_time.required' => 'Office hours start time is required.',
            'office_end_time.required' => 'Office hours end time is required.',
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
