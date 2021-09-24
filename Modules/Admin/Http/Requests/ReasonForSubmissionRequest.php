<?php

namespace Modules\Admin\Http\Requests;

class ReasonForSubmissionRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $reasonid = request('reasonid');
        $previoussequence = request('previoussequence');
        $submissionreason = request('submissionreason');
        return $rules = [
            'submissionreason' => "required",       
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
            'submissionreason.required' => 'Reason for submission is required.',
        ];
    }

}
