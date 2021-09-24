<?php

namespace Modules\Recruitment\Http\Requests;

class RecFeedbackRequest extends Request
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
            'feedback' => "bail|required|max:255|unique:mysql_rec.rec_feedback_lookups,feedback,{$id},id,deleted_at,NULL",
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
            'feedback.required' => 'Feedback is required.',
            'feedback.unique' => 'This Feedback is already added.',
            'crifeedbackteria.max' => 'This Feedback should not exceed 255 characters.',
        ];
    }
}
