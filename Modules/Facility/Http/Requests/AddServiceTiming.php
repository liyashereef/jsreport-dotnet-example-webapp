<?php

namespace Modules\Facility\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddServiceTiming extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'st_time' => 'required',
            'en_time' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'st_time.required' => 'Start Time is mandatory.',
            'en_time.required' => 'End Time is mandatory.',
            
            

            
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
