<?php

namespace Modules\Expense\Http\Requests;

class ExpenseCategoryRequest extends Request
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
            'name' => "bail|required|max:255|unique:expense_category_lookups,name,{$id},id,deleted_at,NULL",
            'short_name' => "bail|nullable|max:255|unique:expense_category_lookups,short_name,{$id},id,deleted_at,NULL",
            'description' => "bail|max:255"
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
            'name.required' => 'Category Name is required.',
            'name.unique' => 'Category Name is already added.',
            'name.max' => 'The category name should not exceed 255 characters.',
            'short_name.unique' => 'Short Name is already added.',
            'short_name.max' => 'The short name should not exceed 255 characters.',
            'description.max' => 'Description should not exceed 255 characters.',
        ];
    }

}
