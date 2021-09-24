<?php

namespace Modules\Recruitment\Http\Requests;

class RecOnboardingDocumentsRequest extends Request
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
            'document_name' => "bail|required|max:255|unique:mysql_rec.rec_onboarding_documents,document_name,{$id},id,deleted_at,NULL",
        
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
            'document_name.required' => 'Document Name is required.',
            'document_name.unique' => 'This document is already added.',
        ];
    }
}
