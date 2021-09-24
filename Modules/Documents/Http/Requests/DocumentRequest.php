<?php

namespace Modules\Documents\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'document_category_id' => 'bail|sometimes|required',
            'document_name_id' => 'bail|required',
            'document_attachment' => 'bail|required',
            'document_description'=> 'bail|max:500'
            
        ];
    }

    public function messages()
    {
        return [
            'document_category_id.required' => 'Please choose the document category.',
            'document_name_id.required' => 'Please enter the document name .',
            'document_attachment.required' => 'Please add the attachment .',
            'document_description.max'=>'The Document Description should not exceed 500 characters.'

            
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
