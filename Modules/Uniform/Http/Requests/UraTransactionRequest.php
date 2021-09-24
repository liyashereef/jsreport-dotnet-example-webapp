<?php

namespace Modules\Uniform\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UraTransactionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'transaction_type' => 'required|numeric',
            'ura_operation_id' => 'required|numeric',
            'user_id' => 'required'
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
