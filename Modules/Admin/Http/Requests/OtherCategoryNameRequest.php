<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OtherCategoryNameRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request('id');
        $other_cat_id = request('other_category_lookup_id');
        return $rules = [

            'name' => "bail|required|max:255|unique:other_category_names,name,{$id},id,other_category_lookup_id,{$other_cat_id},deleted_at,NULL",
            'document_type_id' =>  "bail|required",
            'other_category_lookup_id' =>  "bail|required",
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
            'name.required' => 'Name is required.',
            'name.unique' => 'This Name is already added.',
            'document_type_id.required' => 'Document Type is required.',
            'other_category_lookup_id.required' => 'Category is required.',
       ];
    }
}
