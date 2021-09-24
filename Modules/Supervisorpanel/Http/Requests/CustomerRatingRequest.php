<?php

namespace Modules\Supervisorpanel\Http\Requests;

class CustomerRatingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'rating_id' => 'bail|not_in:0',
            'notes' => 'bail|required|max:256',
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
            'notes.required' => 'Notes is required.',
            'notes.max' => 'Notes should not exceed 256 characters.',
            'rating_id.not_in' => 'Please choose the rating.',
        ];
    }

}
