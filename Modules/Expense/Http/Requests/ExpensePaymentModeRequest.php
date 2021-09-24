<?php

namespace Modules\Expense\Http\Requests;


class ExpensePaymentModeRequest extends Request
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
            'mode_of_payment' => "bail|required|unique:expense_payment_modes,mode_of_payment,{$id},id,deleted_at,NULL",
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
            'mode_of_payment.required' => 'Payment Mode is required.',
            'mode_of_payment.unique' => 'Payment Mode is already added.',
            
        ];
    }
}
