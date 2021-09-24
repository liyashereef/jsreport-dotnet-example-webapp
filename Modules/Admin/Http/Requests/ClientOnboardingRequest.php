<?php

namespace Modules\Admin\Http\Requests;

use App\Rules\DistinctCaseInsensitive;

class ClientOnboardingRequest extends Request
{
	 /**
     * Get the validation rules that apply to the request.
     *
      * @return array
     */
    public function rules()
    {
        $id = request('id');
        $steps = request('steps');
        $sort = request('sort');
        return $rules = [
            'section' => "bail|required|max:250",
            'sort_order' => "bail|numeric|max:99",
            'steps.*' => ['bail', 'required', 'max:250'],
            'sort.*' => 'bail|required|numeric|max:99',
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
            'section.required' => 'Section is required.',
            'section.max'=>'Section should be less than 250 characters',
            'sort_order.max'=>'Sort order should be less than 3 digits',
            'steps.*.required'=>'Step is required.',
            'steps.*.max'=>'Step should be less than 250 characters',
            'sort.*.max'=>'Sort is required.',
        ];
    }
}
