<?php

namespace Modules\Expense\Http\Requests;

class ExpenseGlRequest extends Request
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
            'gl_code' => "bail|required|max:255|unique:expense_gl_codes,gl_code,{$id},id,deleted_at,NULL",
            'short_name' => "bail|max:255",
            'description' => "bail|max:255",
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
            'gl_code.required' => 'GL Code is required.',
            'gl_code.unique' => 'This GL Code is already added.',
            'description.max' => 'The Description should not exceed 255 characters.',
        ];
    }

}
