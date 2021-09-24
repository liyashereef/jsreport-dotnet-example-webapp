<?php

namespace Modules\Hranalytics\Http\Requests;

class CandidateTerminationRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'reason_id' => 'bail|required|numeric',
            'reason' => 'bail|required|max:1000',
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

        ];
    }

}
