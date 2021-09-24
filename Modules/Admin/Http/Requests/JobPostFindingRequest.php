<?php

namespace Modules\Admin\Http\Requests;

class JobPostFindingRequest extends Request
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
            'job_post_finding' => "bail|required|max:255|unique:job_post_finding_lookups,job_post_finding,{$id},id,deleted_at,NULL",
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
            'job_post_finding.required' => 'Job post finding is required.',
            'job_post_finding.unique' => 'This job post finding is already added.',
            'job_post_finding.max' => 'Job post finding should not exceed 255 characters.',
        ];
    }

}
