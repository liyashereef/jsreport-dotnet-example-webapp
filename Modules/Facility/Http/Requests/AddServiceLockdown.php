<?php

namespace Modules\Facility\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddServiceLockdown extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'st_date' => 'nullable',
            'en_date' => 'nullable',
            'st_time' => 'nullable',
            'en_time' => 'nullable|after:st_time',
        ];
    }
    public function messages()
    {
        return [
            'en_date.after' => 'End date should be greater than start date',
            'en_time.after' => 'End time should be greater than start time',




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
