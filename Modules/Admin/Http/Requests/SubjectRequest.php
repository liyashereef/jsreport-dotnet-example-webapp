<?php

namespace Modules\Admin\Http\Requests;

class SubjectRequest extends Request
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
            'subject' => "bail|required|max:255|unique:incident_report_subjects,subject,{$id},id,deleted_at,NULL"
            //'incident_category_id' =>'required|exists:incident_categories,id'
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
            'subject.required' => 'Subject is required.',
            'subject.unique' => 'This Subject is already added.',
            'subject.max' => 'This Subject should not exceed 255 characters.',
           // 'incident_category_id.required' => 'Incident Category is required.',
           // 'incident_category_id.exists' => 'Invalid incident Category.'
        ];
    }

}
