<?php
namespace Modules\Expense\Http\Requests;

class TaxMasterRequest extends Request
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
            //'name' => "bail|required|max:255|unique:expense_tax_masters,name,{$id},id,deleted_at,NULL|regex:/^[\pL\s\-]+$/u",
            'name' => "bail|required|max:255|unique:expense_tax_masters,name,{$id},id,deleted_at,NULL",
            'short_name' => "bail|max:50|nullable|unique:expense_tax_masters,short_name,{$id},id,deleted_at,NULL",
            'tax_percentage' => "bail|required|numeric|regex:/^\d{1,3}+(\.\d{1,2})?$/",
            'effective_from_date' => ['bail', 'required', 'date','after_or_equal:today']
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
            'name.required' => 'Tax master name is required.',
            'name.unique' => 'This Tax master is already added.',
            //'name.regex' =>'Name must contain only letters',
            //'short_name.required' => 'Tax master short name is required.',
            'short_name.unique' => 'This Tax mastershort name is already added.',
            'effective_from_date.required' => 'Effective Date is required.',
            'tax_percentage.numeric' => 'Tax percentage should be numbers',
            'tax_percentage.regex' => 'Tax percentage should be in the format of eg: 100.00'
        ];
    }

}
