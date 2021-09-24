<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $typeid = request('document_type_id');
        return [
            'document_category' => "bail|required|max:255|unique:document_categories,document_category,{$id},id,document_type_id,{$typeid},deleted_at,NULL",
            'document_type_id' =>  "bail|required"
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
             'document_category.required' => 'Document Category is required.',
             'document_category.unique' =>  'This Document Category is already added.',
             'document_type_id.required' => 'Document Type is required.'
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
