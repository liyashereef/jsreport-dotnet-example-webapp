<?php

namespace Modules\Hranalytics\Http\Requests;


class CandidateInterviewRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        return $rules = [
            'interviewer_id' => 'bail|required',
            'candidate_id' => 'bail|required',
            'interview_date' => 'bail|required',
            'interview_notes' => 'bail|required|max:10000',          
            
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages() {
        return [
            
        ];
    }

}
