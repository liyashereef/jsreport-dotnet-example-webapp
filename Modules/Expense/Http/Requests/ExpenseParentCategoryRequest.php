<?php

namespace Modules\Expense\Http\Requests;


class ExpenseParentCategoryRequest extends Request
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
            'parent_category_name' => "bail|required|max:255|unique:expense_parent_categories,parent_category_name,{$id},id,deleted_at,NULL",
            'short_name' => "bail|required|max:255",
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return [
            'parent_category_name.required' => 'Category Name is required.',
            'parent_category_name.unique' => 'This  Category Name is already added.',
            'short_name.required' => 'Short name is required.',
        ];
    }
}
