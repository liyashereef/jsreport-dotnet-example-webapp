<?php

namespace Modules\ClientApp\Http\Requests;

class ClientFeedbackRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $rules = [
            'customerId' => 'bail|required|not_in:0',
            'feedbackTypeId' => 'bail|required|not_in:0',
            'ratingId' => 'bail|required|not_in:0',
            'info' => 'bail|required|max:1000',
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
            'info.required' => 'Feedback is required.',
            'info.max' => 'Feedback should not exceed 1000 characters.',
            'feedbackTypeId.required' => 'Please choose the feedback type.',
            'ratingId.required' => 'Please choose the rating.',
            'ratingId.not_in' => 'Please choose the rating.',
        ];
    }

}
