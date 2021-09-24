<?php

namespace Modules\Admin\Http\Requests;

use Modules\Admin\Rules\YearValidation;

class StatHolidayRequest extends Request
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
            'statholiday' => 'bail|required',
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
            'statholiday.required' => 'Holiday is required.',
            'holiday.unique' => 'Holiday already exist.',
            'description.required' => 'Description is required.',
        ];
    }

}
