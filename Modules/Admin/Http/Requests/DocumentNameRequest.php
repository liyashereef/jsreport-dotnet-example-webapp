<?php

namespace Modules\Admin\Http\Requests;

class DocumentNameRequest extends Request
{
   
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $cat_id = request('document_category_id');
        $subcat_id = request('other_category_name_id');
       
        return $rules = [
            
            'name' => "bail|required|max:255|unique:document_name_details,name,{$id},id,document_category_id,{$cat_id},other_category_name_id,{$subcat_id},deleted_at,NULL",
            'document_type_id' =>  "bail|required",
            'document_category_id' =>  "bail|required",
            'other_category_name_id' => 'bail|sometimes|required_if:document_type_id,==,3'
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
             'name.required' => 'Document Name is required.',
             'name.unique' => 'This Document Name is already added.',
             'name.regex' => 'Document Name is only accept AlphaNumeric.',
             'document_type_id.required' => 'Document Type is required.',
             'document_category_id.required' => 'Document Category is required.',
             'other_category_name_id.required_if' => 'Other Sub Category Name is required.'
        ];
    }
}
