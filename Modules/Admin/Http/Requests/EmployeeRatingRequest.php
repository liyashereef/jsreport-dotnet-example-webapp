<?php

namespace Modules\Admin\Http\Requests;

class EmployeeRatingRequest extends Request
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
            'rating' => "bail|required|max:255|unique:employee_rating_lookups,rating,{$id},id,deleted_at,NULL",
            'score' => "bail|required|numeric|unique:employee_rating_lookups,score,{$id},id,deleted_at,NULL",
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
            'rating.required' => 'Rating is required.',
            'rating.unique' => 'This Rating is already added.',
        ];
    }

}
